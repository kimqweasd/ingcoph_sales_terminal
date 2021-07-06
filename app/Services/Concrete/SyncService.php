<?php

namespace App\Services\Concrete;

use App\Enums\SyncType;
use App\Services\Blueprint\SyncServiceInterface;
use App\Traits\DatabaseTransaction;
use App\Traits\MorphMap;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\Relation;

class SyncService implements SyncServiceInterface
{
    use MorphMap, DatabaseTransaction;

    protected $origin;

    public function start(array $settings)
    {
        $that = $this;

        $this->origin = $this->getMorphMapValue($settings['group'][0]);

        $moduleIsConsistOnlyOfSingleData = $settings['type'] === SyncType::SINGLE;
        $moduleIsPaginatedAndAtFirstPage = $settings['type'] === SyncType::PAGINATED && (int)request()->get('page') === 1;

        if ($moduleIsConsistOnlyOfSingleData || $moduleIsPaginatedAndAtFirstPage) {
            $this->reset($settings['group']);
        }

        return $this->transaction(function() use($that, $settings){return $settings['callback']($that->origin, request()->get('data'));});
    }

    public function reset(array $modules)
    {
        foreach ($modules as $module){
            !is_null($this->getMorphMapValue($module))
                ? $this->getMorphMapValue($module)::query()->truncate()
                : DB::table($module)->truncate();
        }
    }

    private function map(array $modules)
    {
        array_walk($modules, function(&$module){
            $module = !is_null($this->getMorphMapValue($module))
                ? $this->getMorphMapValue($module)
                : DB::table($module);
        });

        return $modules;
    }
}
