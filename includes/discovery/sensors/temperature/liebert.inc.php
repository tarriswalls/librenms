<?php
/**
 * liebert.inc.php
 *
 * LibreNMS temperature discovery module for Liebert
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    LibreNMS
 * @link       http://librenms.org
 * @copyright  2017 Neil Lathwood
 * @author     Neil Lathwood <neil@lathwood.co.uk>
 */

$lib_data = snmpwalk_cache_oid($device, 'lgpEnvTemperatureEntryDegC', array(), 'LIEBERT-GP-ENVIRONMENTAL-MIB');

foreach ($lib_data as $index => $data) {
    if (is_numeric($data['lgpEnvTemperatureMeasurementTenthsDegC'])) {
        $oid        = '.1.3.6.1.4.1.476.1.42.3.4.1.3.3.1.50.' . $index;
        $descr      = $data['lgpEnvTemperatureDescrDegC'];
        $low_limit  = $data['lgpEnvTemperatureLowThresholdTenthsDegC'];
        $high_limit = $data['lgpEnvTemperatureHighThresholdTenthsDegC'];
        $current    = $data['lgpEnvTemperatureMeasurementTenthsDegC'];
        $divisor    = 0.1;
        $new_index  = 'lgpEnvTemperatureMeasurementTenthsDegC.' . $index;
    } elseif (is_numeric($data['lgpEnvTemperatureMeasurementDegC'])) {
        $oid        = '.1.3.6.1.4.1.476.1.42.3.4.1.3.3.1.3.' . $index;
        $descr      = $data['lgpEnvTemperatureDescrDegC'];
        $low_limit  = $data['lgpEnvTemperatureLowThresholdDegC'];
        $high_limit = $data['lgpEnvTemperatureHighThresholdDegC'];
        $current    = $data['lgpEnvTemperatureMeasurementDegC'];
        $divisor    = 1;
        $new_index  = 'lgpEnvTemperatureDescrDegC.' . $index;
    }
    if (is_numeric($current)) {
        discover_sensor($valid['sensor'], 'temperature', $device, $oid, $new_index, 'liebert', $descr, $divisor, '1', $low_limit, null, null, $high_limit, $current);
    }
}

unset(
    $lib_data,
    $current,
    $oid,
    $descr,
    $low_limit,
    $high_limit,
    $divisor,
    $new_index
);

$return_temp = snmp_get($device, 'lgpEnvReturnAirTemperature.0', '-Oqv');
if (is_numeric($return_temp)) {
    $oid   = '.1.3.6.1.4.1.476.1.42.3.4.1.1.2.0';
    $index = 'lgpEnvReturnAirTemperature.0';
    $descr = 'Return Air Temp';
    discover_sensor($valid['sensor'], 'temperature', $device, $oid, $index, 'liebert', $descr, $divisor, '1', null, null, null, null, $return_temp);
}

$supply_temp = snmp_get($device, 'lgpEnvSupplyAirTemperature.0', '-Oqv');
if (is_numeric($supply_temp)) {
    $oid   = '.1.3.6.1.4.1.476.1.42.3.4.1.1.3.0';
    $index = 'lgpEnvSupplyAirTemperature.0';
    $descr = 'Supply Air Temp';
    discover_sensor($valid['sensor'], 'temperature', $device, $oid, $index, 'liebert', $descr, $divisor, '1', null, null, null, null, $supply_temp);
}
