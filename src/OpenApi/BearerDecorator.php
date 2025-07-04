<?php
// src/OpenApi/BearerDecorator.php
namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\SecurityScheme;
use ApiPlatform\OpenApi\OpenApi;

final class BearerDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private readonly OpenApiFactoryInterface $decorated
    ) {}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        /* 1. Declare / merge the scheme */
        $components      = $openApi->getComponents();
        $schemes         = $components->getSecuritySchemes() ?? [];

        $schemes['bearerAuth'] = new SecurityScheme(
            type: 'http',
            scheme: 'bearer',
            bearerFormat: 'JWT'
        );

        $components = $components->withSecuritySchemes($schemes);

        /* 2. Require it globally (every path) */
        $openApi = $openApi->withSecurity([['bearerAuth' => []]]);

        return $openApi;
    }
}
