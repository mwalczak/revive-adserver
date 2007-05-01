<?php

/*
+---------------------------------------------------------------------------+
| Openads v2.3                                                              |
| ============                                                              |
|                                                                           |
| Copyright (c) 2003-2007 Openads Limited                                   |
| For contact details, see: http://www.openads.org/                         |
|                                                                           |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU General Public License as published by      |
| the Free Software Foundation; either version 2 of the License, or         |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU General Public License for more details.                              |
|                                                                           |
| You should have received a copy of the GNU General Public License         |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id$
*/

require_once MAX_PATH . '/lib/OA/Admin/Statistics/Delivery/CommonDaily.php';

/**
 * The class to display the delivery statistcs for the page:
 *
 * Statistics -> Publishers & Zones -> Zone Overview -> Zone History -> Daily Statistics
 *
 * and:
 *
 * Statistics -> Publishers & Zones -> Zone Overview -> Campaign Distribution -> Distribution History -> Daily Statistics
 *
 * @package    OpenadsAdmin
 * @subpackage StatisticsDelivery
 * @author     Matteo Beccati <matteo@beccati.com>
 * @author     Andrew Hill <andrew.hill@openads.org>
 */
class OA_Admin_Statistics_Delivery_Controller_ZoneDaily extends OA_Admin_Statistics_Delivery_CommonDaily
{

    function start()
    {
        // Get the preferences
        $pref = $GLOBALS['_MAX']['PREF'];

        // Get parameters
        if (phpAds_isUser(phpAds_Affiliate)) {
            $publisherId = phpAds_getUserId();
        } else {
            $publisherId = (int)MAX_getValue('affiliateid', '');
        }
        $zoneId      = (int)MAX_getValue('zoneid', '');

        // Cross-entity
        $placementId = (int)MAX_getValue('campaignid', '');
        $adId        = (int)MAX_getValue('bannerid', '');

        // Security check
        phpAds_checkAccess(phpAds_Admin + phpAds_Agency + phpAds_Affiliate);
        if (!MAX_checkZone($publisherId, $zoneId)) {
            phpAds_PageHeader('2');
            phpAds_Die ($GLOBALS['strAccessDenied'], $GLOBALS['strNotAdmin']);
        }

        if (!empty($adId)) {
            // Fetch banners
            $aAds = $this->getPublisherBanners($publisherId);

            // Cross-entity security check
            if (!isset($aAds[$adId])) {
                $this->noStatsAvailable = true;
            }
        } elseif (!empty($placementId)) {
            // Fetch campaigns
            $aPlacements = $this->getPublisherCampaigns($publisherId);

            // Cross-entity security check
            if (!isset($aPlacements[$placementId])) {
                $this->noStatsAvailable = true;
            }
        }

        // HTML Framework
        if (phpAds_isUser(phpAds_Admin) || phpAds_isUser(phpAds_Agency)) {
            if (empty($placementId) && empty($adId)) {
                $this->pageId = '2.4.2.1.1';
            } else {
                // Cross-entity
                $this->pageId = empty($adId) ? '2.4.2.2.1.1' : '2.4.2.2.2.1';
            }
            $this->aPageSections = array($this->pageId);
        } elseif (phpAds_isUser(phpAds_Client)) {
            if (empty($placementId) && empty($adId)) {
                $this->pageId = '1.2.1.1';
            } else {
                // Cross-entity
                $this->pageId = empty($adId) ? '1.2.2.1.1' : '1.2.2.2.1';
            }
            $this->aPageSections = array($this->pageId);
        }

        // Add standard page parameters
        $this->aPageParams = array('affiliateid' => $publisherId, 'zoneid' => $zoneId,
                                  'entity' => 'zone', 'breakdown' => 'daily',
                                  'day' => MAX_getValue('day', '')
                                 );
        $this->aPageParams['period_preset'] = MAX_getStoredValue('period_preset', 'today');
        $this->aPageParams['statsBreakdown'] = MAX_getStoredValue('statsBreakdown', 'day');

        // Cross-entity
        if (!empty($adId)) {
            $this->aPageParams['campaignid'] = $aAds[$adId]['placement_id'];
            $this->aPageParams['banner']     = $adId;
        } elseif (!empty($placementId)) {
            $this->aPageParams['campaignid'] = $placementId;
        }

        $this->_loadParams();

        $this->_addBreadcrumbs('zone', $zoneId);

        // Cross-entity
        if (!empty($adId)) {
            $this->addCrossBreadcrumbs('banner', $adId);
        } elseif (!empty($placementId)) {
            $this->addCrossBreadcrumbs('campaign', $placementId);
        }

        // Add shortcuts
        if (!phpAds_isUser(phpAds_Affiliate)) {
            $this->_addShortcut(
                $GLOBALS['strAffiliateProperties'],
                'affiliate-edit.php?affiliateid='.$publisherId,
                'images/icon-affiliate.gif'
            );
        }

        $this->_addShortcut(
            $GLOBALS['strZoneProperties'],
            'zone-edit.php?affiliateid='.$publisherId.'&zoneid='.$zoneId,
            'images/icon-zone.gif'
        );

        $aParams = array();
        $aParams['zone_id'] = $zoneId;

        // Cross-entity
        if (!empty($adId)) {
            $aParams['ad_id'] = $adId;
        } elseif (!empty($placementId)) {
            $aParams['placement_id'] = $placementId;
        }

        $this->prepareHistory($aParams);
    }

}

?>