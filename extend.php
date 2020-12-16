<?php
function bootSearchable()
    {
        Query::extend('chunkById', function($query, $count, callable $callback, $column = null, $alias = null){
              $column = is_null($column) ? $query->getModel()->getKeyName() : $column;
              $alias = is_null($alias) ? $column : $alias;

              $lastId = 0;

              do {
                  $clone = clone $query;

                  $results = $clone->forPageAfterId($count, $lastId, $column)->select();
                  $results = static::newCollection($results->all());

                  $countResults = $results->count();

                  if ($countResults == 0) {
                      break;
                  }
                  if ($callback($results) === false) {
                      return false;
                  }

                  $lastId = $results->last()->{$alias};

                  unset($results);
              } while ($countResults == $count);

              return true;
        });

        Query::extend('forPageAfterId', function(Query $query, $perPage = 15, $lastId = 0, $column = 'id'){
            $query->setOption('order', collect($query->getOptions('order'))->filter(function($order, $key) use ($column){
                return empty($key) ? $order == $column : $key == $column;
            })->all());

            return $query->where($column, '>', $lastId)
                        ->order($column, 'asc')
                        ->limit($perPage);
        });
    }
