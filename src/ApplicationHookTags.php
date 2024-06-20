<?php

namespace Takemo101\Chubby;

/**
 * This class defines the tags for the application hooks.
 */
final class ApplicationHookTags
{
    /**
     * This hook occurs before the configuration process of the Slim application.
     * You can obtain the `Slim\App` instance.
     */
    public const Http_BeforeSlimConfiguration = 'http.before_slim_configuration';

    /**
     * This hook occurs after the configuration process of the Slim application.
     * You can obtain the `Slim\App` instance.
     */
    public const Http_AfterSlimConfiguration = 'http.after_slim_configuration';

    /**
     * This hook occurs before adding routing middleware.
     * You can obtain the `Slim\App` instance.
     */
    public const Http_BeforeAddRoutingMiddleware = 'http.before_add_routing_middleware';

    /**
     * This hook occurs after adding routing middleware.
     * You can obtain the `Slim\App` instance.
     */
    public const Http_AfterAddRoutingMiddleware = 'http.after_add_routing_middleware';

    /**
     * This hook occurs before controller execution.
     * You can obtain the `Psr\Http\Message\ServerRequestInterface` instance.
     */
    public const Http_BeforeControllerExecution = 'http.before_controller_execution';

    /**
     * This hook occurs after controller execution.
     * You can obtain the `Psr\Http\Message\ResponseInterface` instance.
     */
    public const Http_AfterControllerExecution = 'http.after_controller_execution';

    /**
     * This hook occurs after creating the request context.
     * You can obtain the `Takemo101\Chubby\Http\RequestContext` instance.
     */
    public const Http_RequestContextCreated = 'http.request_context_created';

    /**
     * This hook occurs after clearing the request context.
     * You can obtain the `Takemo101\Chubby\Http\RequestContext` instance.
     */
    public const Http_RequestContextCleared = 'http.request_context_cleared';

    /**
     * This hook occurs before receiving the request.
     * You can obtain the `Psr\Http\Message\ServerRequestInterface` instance.
     */
    public const Http_RequestReceived = 'http.request_received';

    /**
     * This hook occurs before sending the response.
     * You can obtain the `Psr\Http\Message\ResponseInterface` instance.
     */
    public const Http_ResponseSending = 'http.response_sending';
}
