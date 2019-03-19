<?php
declare(strict_types = 1);
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

namespace Phauthentic\Authorization\Exception;

use Phauthentic\Authorization\Policy\ResultInterface;

/**
 * Forbidden Exception
 */
class ForbiddenException extends Exception
{
    /**
     * {@inheritDoc}
     */
    protected $code = 403;

    /**
     * {@inheritDoc}
     */
    protected $messageTemplate = 'Identity is not authorized to perform `%s` on `%s`.';
    /**
     * Policy check result.
     *
     * @var \ Phauthentic\Authorization\Policy\ResultInterface|null
     */
    protected $result;

    /**
     * Returns policy check result if passed to the exception.
     *
     * @param \Phauthentic\Authorization\Policy\ResultInterface|null $result Result
     * @return $this
     */
    public function setResult(?ResultInterface $result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Returns policy check result if passed to the exception.
     *
     * @return \Phauthentic\Authorization\Policy\ResultInterface|null
     */
    public function getResult(): ?ResultInterface
    {
        return $this->result;
    }
}
