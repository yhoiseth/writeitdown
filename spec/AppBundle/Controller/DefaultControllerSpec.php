<?php

namespace spec\AppBundle\Controller;

use AppBundle\Controller\DefaultController;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DefaultControllerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(DefaultController::class);
    }

    function it_increments_slugs()
    {
        $this->incrementSlug('slug')->shouldReturn('slug-2');
        $this->incrementSlug('slug-2')->shouldReturn('slug-3');
        $this->incrementSlug('slug-3')->shouldReturn('slug-4');
        $this->incrementSlug('slug-38')->shouldReturn('slug-39');
        $this->incrementSlug('slug-with-several-words')->shouldReturn('slug-with-several-words-2');
        $this->incrementSlug('slug-with-several-words-2')->shouldReturn('slug-with-several-words-3');
        $this->incrementSlug('slug-with-several-words-3')->shouldReturn('slug-with-several-words-4');
        $this->incrementSlug('slug-with-several-words-13')->shouldReturn('slug-with-several-words-14');
    }
}
