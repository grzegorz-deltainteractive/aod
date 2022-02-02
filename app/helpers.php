<?php
/**
 * Created by Grzegorz Możdżeń <grzegorz.mozdzen@oxm.pl>
 * Date: 25/01/2022
 * Time: 23:18
 */

if (!function_exists('getBreadcrumbs')) {
    function getBreadcrumbs($segments)
    {
        $translations = [
            'suppliers' => 'Dostawcy',
            'pools' => 'Ankiety',
            'laboratories' => 'Laboratoria',
            'departments' => 'Działy',
            'settings' => 'Ustawienia',
            'users' => 'Użytkownicy',
            'roles' => 'Uprawnienia',
            'edit' => 'Edytuj',
            'categories' => 'Kategorie ankiety',
            'displaypools' => 'Średnia ankiet',
            'draws' => 'Wykres parametru',
            'listpools' => 'Lista ankiet dla dostawcy',
            'singlepool' => 'Pojedyńcza ankieta'
        ];
        foreach ($segments as &$segment) {
            if (array_key_exists($segment, $translations)) {
                $segment = [
                    'title' => $translations[$segment],
                    'url' => $segment
                ];
            } else {
                $segment = [
                    'title' => $segment,
                    'url' => ''
                ];
            }
        }
        unset ($segment);
        return $segments;
    }
}
function median($values) {
    $values = array_values($values);
    $count = count($values);
    if ($count === 0)  return 0;
    asort($values);
    $half = floor($count / 2);
    if ($count % 2) return $values[$half];
    return ($values[$half - 1] + $values[$half]) / 2.0;
}


/**
 * check logged user can fill pool
 * @return bool
 */
function canFillPool()
{
    $user = Auth::user();
    $allowedRoles = [1, 4, 5, 6, 7, 8];
    if (in_array($user->role_id, $allowedRoles)) {
        return true;
    }
    return false;
}

function canAcceptPool()
{
    $user = Auth::user();
    $allowedRoles = [1, 6, 7, 8];
    if (in_array($user->role_id, $allowedRoles)) {
        return true;
    }
    return false;
}

/**
 * check display pool row data
 * @param $visibleForLab
 * @return bool
 */
function checkDisplayField($visibleForLab) {
    $user = Auth::user();
    $displayAll = [1,6,7,8];
    if (in_array($user->role_id, $displayAll)) {
        // user ma możliwość wypełnienia wszystkiego
        return true;
    }
    if ($visibleForLab == 1) {
        // pokazać tylko dla laboratorium
        if ($user->role_id == 4) {
            return true;
        }
    } else {
        // pokazać tylko dla biura
        if ($user->role_id == 5) {
            return true;
        }
    }
    return false;
}
