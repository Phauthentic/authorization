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
use Phauthentic\Authorization\Middleware\UnauthorizedHandler\ExceptionHandler;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use PHPUnit\Framework\TestCase;

class ExceptionHandlerTest extends TestCase
{
    public function testHandle()
    {
        $handler = new ExceptionHandler();

        $exception = new Exception();
        $request = new ServerRequest();
        $response = new Response();

        $this->expectException(Exception::class);
        $handler->handle($exception, $request, $response);
    }
}
