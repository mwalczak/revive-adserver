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
 * Statistics -> Global History
 *
 * @package    OpenadsAdmin
 * @subpackage StatisticsDelivery
 * @author     Matteo Beccati <matteo@beccati.com>
 * @author     Andrew Hill <andrew.hill@openads.org>
 */
class OA_Admin_Statistics_Delivery_Controller_GlobalDaily extends OA_Admin_Statistics_Delivery_CommonDaily
{

    function start()
    {
        // Security check
        phpAds_checkAccess(phpAds_Admin + phpAds_Agency);

        // Get the preferences
        $pref = $GLOBALS['_MAX']['PREF'];

        // Add module page parameters
        $this->aPageParams['entity'] = 'global';
        $this->aPageParams['breakdown'] = 'daily';
        $this->aPageParams['period_preset'] = MAX_getStoredValue('period_preset', 'today');
        $this->aPageParams['statsBreakdown'] = MAX_getStoredValue('statsBreakdown', 'day');

        $this->_loadParams();

        // HTML Framework
        $this->pageId = '2.2.1';
        $this->aPageSections = array('2.1.1');

        $aParams = array();
        if (phpAds_isUser(phpAds_Agency)) {
            $aParams['agency_id'] = phpAds_getAgencyID();
        }

        $this->prepareHistory($aParams);
    }

}

?>