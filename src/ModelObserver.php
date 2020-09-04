<?php

namespace brooke\scout;

use think\model\concern\SoftDelete;

class ModelObserver
{
    /**
     * The class names that syncing is disabled for.
     *
     * @var array
     */
    protected static $syncingDisabledFor = [];

    /**
     * Enable syncing for the given class.
     *
     * @param  string  $class
     * @return void
     */
    public static function enableSyncingFor($class)
    {
        unset(static::$syncingDisabledFor[$class]);
    }

    /**
     * Disable syncing for the given class.
     *
     * @param  string  $class
     * @return void
     */
    public static function disableSyncingFor($class)
    {
        static::$syncingDisabledFor[$class] = true;
    }

    /**
     * Determine if syncing is disabled for the given class or model.
     *
     * @param  object|string  $class
     * @return bool
     */
    public static function syncingDisabledFor($class)
    {
        $class = is_object($class) ? get_class($class) : $class;

        return isset(static::$syncingDisabledFor[$class]);
    }

    /**
     * Handle the saved event for the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function afterWrite($model)
    {
        if (static::syncingDisabledFor($model)) {
            return;
        }

        if (! $model->shouldBeSearchable()) {
            $model->unsearchable();
            return;
        }

        $model->searchable();
    }

    /**
     * Handle the deleted event for the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function afterDelete($model)
    {
        if (static::syncingDisabledFor($model)) {
            return;
        }

        if($this->usesSoftDelete($model) && ! $model->trashed()){
            $model->unsearchable();
        }

        if ($this->usesSoftDelete($model) && config('scout.soft_delete', false)) {
            $this->saved($model);
        } else {
            $model->unsearchable();
        }
    }

    /**
     * Handle the restored event for the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function afterRestore($model)
    {
        $this->saved($model);
    }

    /**
     * Determine if the given model uses soft deletes.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return bool
     */
    protected function usesSoftDelete($model)
    {
        return in_array(SoftDelete::class, class_uses_recursive($model));
    }
}
