<?php
/*
 * vim:set softtabstop=4 shiftwidth=4 expandtab:
 *
 * LICENSE: GNU Affero General Public License, version 3 (AGPL-3.0-or-later)
 * Copyright 2001 - 2020 Ampache.org
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

namespace Ampache\Module\Catalog\Update;

use Ahc\Cli\IO\Interactor;

interface UpdateCatalogInterface
{
    public function update(
        Interactor $interactor,
        bool $deactivateMemoryLimit,
        bool $addNew,
        bool $addArt,
        bool $importPlaylists,
        bool $cleanup,
        bool $verification,
        bool $updateInfo,
        bool $optimizeDatabase,
        ?string $catalogName,
        string $catalogType
    ): void;

    /**
     * Reduce the Id numbers of large file systems (MariaDB ONLY)
     */
    public function compactIds(
        Interactor $interactor,
        bool $dryRun
    ): void;

    /**
     * Updates the filesystem path of all files related to this catalog
     */
    public function updatePath(
        Interactor $interactor,
        string $catalogType,
        ?string $catalogName,
        ?string $newPath
    ): void;
}
