<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         1.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Authorization\Test\TestCase\Middleware\UnauthorizedHandler;

use Phauthentic\Authorization\Exception\Exception;
use Phauthentic\Authorization\Middleware\UnauthorizedHandler\RedirectHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * RedirectHandlerTest
 */
class RedirectHandlerTest extends TestCase
{
    /**
     * @return void
     */
    public function testHandleRedirection(): void
    {
        $handler = new RedirectHandler();
        $exception = new Exception();

        $request = $this->getMockBuilder(ServerRequestInterface::class)
            ->getMock();

        $request->expects($this->any())
            ->method('getMethod')
            ->willReturn('GET');

        $request->expects($this->atLeastOnce())
            ->method('getRequestTarget')
            ->willReturn('/');

        $response = $this->getMockBuilder(ResponseInterface::class)
            ->getMock();

        $response->expects($this->any())
            ->method('withHeader')
            ->with('Location', '/?redirect=%2F')
            ->willReturnSelf();

        $response->expects($this->any())
            ->method('withStatus')
            ->with(302)
            ->willReturnSelf();

        $response = $handler->handle($exception, $request, $response, [
            'exceptions' => [
                Exception::class,
            ],
        ]);
    }

    /**
     * testHandleRedirectionWithQuery
     *
     * @return void
     */
    public function testHandleRedirectionWithQuery(): void
    {
        $handler = new RedirectHandler();
        $exception = new Exception();

        $request = $this->getMockBuilder(ServerRequestInterface::class)
            ->getMock();

        $request->expects($this->any())
            ->method('getMethod')
            ->willReturn('GET');

        $request->expects($this->atLeastOnce())
            ->method('getRequestTarget')
            ->willReturn('/path?key=value');

        $response = $this->getMockBuilder(ResponseInterface::class)
            ->getMock();

        $response->expects($this->any())
            ->method('withHeader')
            ->with('Location', '/login?foo=bar&redirect=%2Fpath%3Fkey%3Dvalue')
            ->willReturnSelf();

         $response->expects($this->any())
             ->method('withStatus')
             ->with(302)
             ->willReturnSelf();

        $response = $handler->handle($exception, $request, $response, [
            'exceptions' => [
                Exception::class,
            ],
            'url' => '/login?foo=bar'
        ]);

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     *
     */
    public function testHandleRedirectionNoQuery(): void
    {
        $handler = new RedirectHandler();
        $exception = new Exception();

        $request = $this->getMockBuilder(ServerRequestInterface::class)
            ->getMock();

        $request->expects($this->any())
            ->method('getMethod')
            ->willReturn('GET');

        $response = $this->getMockBuilder(ResponseInterface::class)
            ->getMock();

        $response->expects($this->any())
            ->method('withHeader')
            ->with('Location', '/users/login')
            ->willReturnSelf();

        $response->expects($this->any())
            ->method('withStatus')
            ->with(302)
            ->willReturnSelf();

        $response = $handler->handle($exception, $request, $response, [
            'exceptions' => [
                Exception::class,
            ],
            'url' => '/users/login',
            'queryParam' => null,
        ]);

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @dataProvider httpMethodProvider
     */
    public function testHandleRedirectionIgnoreNonIdempotentMethods($method)
    {
        $this->markTestSkipped();
        $handler = new RedirectHandler();
        $exception = new Exception();

        $request = $this->getMockBuilder(ServerRequestInterface::class)
            ->getMock();

        $request->expects($this->any())
            ->method('getMethod')
            ->willReturn($method);

        $request->expects($this->atLeastOnce())
            ->method('getRequestTarget')
            ->willReturn('/path?key=value');

        $response = $this->getMockBuilder(ResponseInterface::class)
            ->getMock();

        $response->expects($this->any())
            ->method('withHeader')
            ->with('Location', '/login?foo=bar')
            ->willReturnSelf();

        $response->expects($this->any())
            ->method('withStatus')
            ->with(302)
            ->willReturnSelf();

        $response = $handler->handle($exception, $request, $response, [
            'exceptions' => [
                Exception::class,
            ],
            'url' => '/login?foo=bar'
        ]);

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * testHandleException
     *
     * @return void
     */
    public function testHandleException(): void
    {
        $handler = new RedirectHandler();
        $exception = new Exception();

        $request = $this->getMockBuilder(ServerRequestInterface::class)
            ->getMock();

        $response = $this->getMockBuilder(ResponseInterface::class)
            ->getMock();

        $this->expectException(Exception::class);
        $handler->handle($exception, $request, $response);
    }

    /**
     * Http Method Provider
     *
     * @return array
     */
    public function httpMethodProvider(): array {
        return [
            ['POST'],
            ['PUT'],
            ['DELETE'],
            ['PATCH'],
            ['OPTIONS'],
            ['HEAD'],
        ];
    }
}
