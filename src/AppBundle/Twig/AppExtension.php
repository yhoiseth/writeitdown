<?php

namespace AppBundle\Twig;


use Stringy\Stringy;

class AppExtension extends \Twig_Extension
{
    /**
     * @var Stringy
     */
    private $stringy;

    /**
     * @param Stringy $stringy
     */
    public function __construct(Stringy $stringy)
    {
        $this->setStringy($stringy);
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter(
                'html_headings_increment',
                [$this, 'incrementHtmlHeadings']
            ),
            new \Twig_SimpleFilter(
                'html_purify',
                [$this, 'purify'],
                [
                    'is_safe' => [
                        'html'
                    ]
                ]
            ),
        ];
    }

    public function incrementHtmlHeadings(string $text): string
    {
        $stringy = $this->getStringy();

        $text = $stringy::create($text);

        $text = $text->replace('<h1', '<h2');
        $text = $text->replace('</h1', '</h2');

        return $text->__toString();
    }

    public function purify($text)
    {
        $elements = array(
            'p',
            'br',
            'small',
            'strong', 'b',
            'em', 'i',
            'strike',
            'sub', 'sup',
            'ins', 'del',
            'ol', 'ul', 'li',
            'h1', 'h2', 'h3',
            'dl', 'dd', 'dt',
            'pre', 'code', 'samp', 'kbd',
            'q', 'blockquote', 'abbr', 'cite',
            'table', 'thead', 'tbody', 'th', 'tr', 'td',
            'a[href|target|rel|id]',
            'img[src|title|alt|width|height|style]'
        );

        $config = \HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', implode(',', $elements));

        $purifier = new \HTMLPurifier($config);

        return $purifier->purify($text);
    }

    /**
     * @return Stringy
     */
    public function getStringy(): Stringy
    {
        return $this->stringy;
    }

    /**
     * @param Stringy $stringy
     */
    public function setStringy(Stringy $stringy)
    {
        $this->stringy = $stringy;
    }
}
