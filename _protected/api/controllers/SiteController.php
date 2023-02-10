<?php

namespace api\controllers;

/**
 * Site controller.
 * It is responsible for displaying static pages, and logging users in and out.
 */
class SiteController extends ApiController
{
    /**
     * Displays the entry data.
     *
     * @return array
     */
    public function actionIndex()
    {
        return ['success' => true, 'data' => 'Welcome to API v0.1'];
    }
}