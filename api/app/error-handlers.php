<?php

use Symfony\Component\HttpKernel\Exception;

App::error(function (Exception\MethodNotAllowedHttpException $e) {

	return Response::notAllowed();

});

App::error(function (Exception\NotFoundHttpException $e) {

	return Response::notFound();

});