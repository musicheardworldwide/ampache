<?php

/*
 * vim:set softtabstop=4 shiftwidth=4 expandtab:
 *
 *  LICENSE: GNU Affero General Public License, version 3 (AGPL-3.0-or-later)
 * Copyright 2001 - 2022 Ampache.org
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

declare(strict_types=0);

namespace Ampache\Module\Api\Method\Api5;

use Ampache\Config\AmpConfig;
use Ampache\Repository\Model\Podcast;
use Ampache\Repository\Model\User;
use Ampache\Module\Api\Api5;

/**
 * Class PodcastEdit5Method
 */
final class PodcastEdit5Method
{
    public const ACTION = 'podcast_edit';

    /**
     * podcast_edit
     * MINIMUM_API_VERSION=420000
     * CHANGED_IN_API_VERSION=5.0.0
     * Update the description and/or expiration date for an existing podcast.
     * Takes the podcast id to update with optional description and expires parameters.
     *
     * @param array $input
     * @param User|null $user
     * filter      = (string) Alpha-numeric search term
     * feed        = (string) feed url (xml!) //optional
     * title       = (string) title string //optional
     * website     = (string) source website url //optional
     * description = (string) //optional
     * generator   = (string) //optional
     * copyright   = (string) //optional
     * @return boolean
     */
    public static function podcast_edit(array $input, ?User $user): bool
    {
        if (!AmpConfig::get('podcast')) {
            Api5::error(T_('Enable: podcast'), '4703', self::ACTION, 'system', $input['api_format']);

            return false;
        }
        if (!Api5::check_access('interface', 50, $user->id, self::ACTION, $input['api_format'])) {
            return false;
        }
        if (!Api5::check_parameter($input, array('filter'), self::ACTION)) {
            return false;
        }
        $podcast_id = $input['filter'];
        $podcast    = new Podcast($podcast_id);

        if (!$podcast->id) {
            /* HINT: Requested object string/id/type ("album", "myusername", "some song title", 1298376) */
            Api5::error(sprintf(T_('Not Found: %s'), $podcast_id), '4704', self::ACTION, 'filter', $input['api_format']);

            return false;
        }

        $feed        = (array_key_exists('feed', $input) && filter_var($input['feed'], FILTER_VALIDATE_URL)) ? filter_var($input['feed'], FILTER_VALIDATE_URL) : $podcast->feed;
        $title       = (array_key_exists('title', $input)) ? scrub_in($input['title']) : $podcast->title;
        $website     = (array_key_exists('website', $input) && filter_var($input['website'], FILTER_VALIDATE_URL)) ? filter_var($input['website'], FILTER_VALIDATE_URL) : $podcast->website;
        $description = (array_key_exists('description', $input)) ? scrub_in($input['description']) : $podcast->description;
        $generator   = (array_key_exists('generator', $input)) ? scrub_in($input['generator']) : $podcast->generator;
        $copyright   = (array_key_exists('copyright', $input)) ? scrub_in($input['copyright']) : $podcast->copyright;
        $data        = array(
            'feed' => $feed,
            'title' => $title,
            'website' => $website,
            'description' => $description,
            'generator' => $generator,
            'copyright' => $copyright
        );
        if ($podcast->update($data)) {
            Api5::message('podcast ' . $podcast_id . ' updated', $input['api_format']);
        } else {
            /* HINT: Requested object string/id/type ("album", "myusername", "some song title", 1298376) */
            Api5::error(sprintf(T_('Bad Request: %s'), $podcast_id), '4710', self::ACTION, 'system', $input['api_format']);
        }

        return true;
    }
}
