<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\Data\Redeem;

/**
 * Interface ResponseInterface
 * @package Magestore\Giftvoucher\Api\Data\Redeem
 */
interface ResponseInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ERRORS = 'errors';
    const SUCCESS = 'success';
    const NOTICES = 'notices';

    /**
     * Get errors messages
     *
     * @return string
     */
    public function getErrors();

    /**
     * Get success messages
     *
     * @return string
     */
    public function getSuccess();


    /**
     *  Get notice messages
     * @return string
     */
    public function getNotices();

    /**
     *  Set errors messages
     * @param string[] $errors
     * @return string
     */
    public function setErrors($errors);

    /**
     *  Set success messages
     * @param string[] $success
     * @return string
     */
    public function setSuccess($success);

    /**
     *  Set notice messages
     * @param string[] $notices
     * @return string
     */
    public function setNotices($notices);
}
