<?php
namespace FOA\Responder_Bundle;

use Aura\Accept\AcceptFactory;
use Aura\Html\HelperLocatorFactory;
use Aura\View\ViewFactory;
use Aura\Web\WebFactory;
use FOA\DomainPayload\PayloadFactory;

class AuraResponderTest extends \PHPUnit_Framework_TestCase
{
    protected $response;

    protected $responder;

    public function setUp()
    {
        $web_factory = new WebFactory($GLOBALS);
        $this->response = $web_factory->newResponse();

        $accept_factory = new AcceptFactory($_SERVER);
        $accept = $accept_factory->newInstance();

        $factory = new HelperLocatorFactory;
        $helpers = $factory->newInstance();
        $view_factory = new ViewFactory();
        $view = $view_factory->newInstance($helpers);
        $view_registry = $view->getViewRegistry();
        $view_registry->set('hello', function () {
            echo "Hello " . $this->name;
        });

        $renderer = new AuraRenderer($view);

        $this->responder = new FakeResponder($accept, $this->response, $renderer);

        $payload_factory = new PayloadFactory();
        $this->responder->setPayload($payload_factory->found(array('name' => 'Hari')));
    }

    public function testRenderView()
    {
        $this->responder->__invoke();
        $this->assertSame('Hello Hari', $this->response->content->get());
    }
}
