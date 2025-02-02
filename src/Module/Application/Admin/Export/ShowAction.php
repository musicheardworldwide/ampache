<?php

declare(strict_types=1);

/**
 * vim:set softtabstop=4 shiftwidth=4 expandtab:
 *
 * LICENSE: GNU Affero General Public License, version 3 (AGPL-3.0-or-later)
 * Copyright Ampache.org, 2001-2024
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */

namespace Ampache\Module\Application\Admin\Export;

use Ampache\Module\Application\ApplicationActionInterface;
use Ampache\Module\Application\Exception\AccessDeniedException;
use Ampache\Module\Authorization\AccessLevelEnum;
use Ampache\Module\Authorization\GuiGatekeeperInterface;
use Ampache\Module\Catalog\CatalogLoaderInterface;
use Ampache\Module\Catalog\Export\CatalogExportTypeEnum;
use Ampache\Module\Util\UiInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Renders the catalog export view
 */
final class ShowAction implements ApplicationActionInterface
{
    public const REQUEST_KEY = 'show';

    private UiInterface $ui;

    private CatalogLoaderInterface $catalogLoader;

    public function __construct(
        UiInterface $ui,
        CatalogLoaderInterface $catalogLoader
    ) {
        $this->ui            = $ui;
        $this->catalogLoader = $catalogLoader;
    }

    public function run(ServerRequestInterface $request, GuiGatekeeperInterface $gatekeeper): ?ResponseInterface
    {
        if ($gatekeeper->mayAccess(AccessLevelEnum::TYPE_INTERFACE, AccessLevelEnum::LEVEL_MANAGER) === false) {
            throw new AccessDeniedException();
        }

        $this->ui->showHeader();
        $this->ui->show(
            'show_export.inc.php',
            [
                'catalogs' => $this->catalogLoader->getCatalogs(),
                'exportTypes' => [
                    CatalogExportTypeEnum::CSV => T_('CSV'),
                    CatalogExportTypeEnum::ITUNES => T_('iTunes'),
                ],
            ]
        );
        $this->ui->showQueryStats();
        $this->ui->showFooter();

        return null;
    }
}
