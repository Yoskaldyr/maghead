<?php

namespace Maghead;

use ArrayAccess;
use Exception;

class Config implements ArrayAccess
{
    public $stash = [];

    protected $classMap = [];

    const DEFAULT_BASE_COLLECTION_CLASS = '\\Maghead\\Runtime\\BaseCollection';

    const DEFAULT_BASE_MODEL_CLASS = '\\Maghead\\Runtime\\BaseModel';

    const DEFAULT_AUTO_ID_COLUMN_CLASS = '\\Maghead\\Schema\\Column\\AutoIncrementPrimaryKeyColumn';

    public function __construct(array $stash)
    {
        $this->stash = $stash;
    }

    /**
     * run bootstrap code.
     */
    public function getBootstrap()
    {
        if (isset($this->stash['cli']['bootstrap'])) {
            return (array) $this->stash['cli']['bootstrap'];
        }
    }

    /**
     * load external schema loader.
     */
    public function getExternalSchemaLoader()
    {
        if (isset($this->stash['schema']['loader'])) {
            return $this->stash['schema']['loader'];
        }
    }

    public function getClassMap()
    {
        return $this->classMap;
    }

    public function removeDataSource($dataSourceId)
    {
        unset($this->stash['data_source']['nodes'][ $dataSourceId ]);
    }

    public function addDataSource($dataSourceId, array $config)
    {
        $this->stash['data_source']['nodes'][ $dataSourceId ] = $config;
    }

    /**
     * get all data sources.
     *
     * @return array data source
     */
    public function getDataSources()
    {
        if (isset($this->stash['data_source']['nodes'])) {
            return $this->stash['data_source']['nodes'];
        }

        return array();
    }

    public function getDataSourceIds()
    {
        if (isset($this->stash['data_source']['nodes'])) {
            return array_keys($this->stash['data_source']['nodes']);
        }

        return [];
    }

    public function getMasterDataSource()
    {
        $id = $this->getMasterDataSourceId();

        if (isset($this->stash['data_source']['nodes'][$id])) {
            return $this->stash['data_source']['nodes'][$id];
        }
    }

    public function setMasterDataSourceId($id)
    {
        $this->stash['data_source']['master'] = $id;
    }

    public function getMasterDataSourceId()
    {
        if (isset($this->stash['data_source']['master'])) {
            return $this->stash['data_source']['master'];
        }
        throw new Exception('master data source is undefined.');
    }

    public function getSeedScripts()
    {
        if (isset($this->stash['seeds'])) {
            return $this->stash['seeds'];
        }
    }

    public function getCacheConfig()
    {
        if (isset($this->stash['cache'])) {
            return $this->stash['cache'];
        }
    }

    /**
     * get data source by data source id.
     *
     * @param string $sourceId
     */
    public function getDataSource($sourceId)
    {
        if (isset($this->stash['data_source']['nodes'][$sourceId])) {
            return $this->stash['data_source']['nodes'][$sourceId];
        }
        throw new Exception("data source $sourceId is not defined.");
    }

    /**
     * get schema config.
     *
     * @return array config
     */
    public function getSchema()
    {
        return isset($this->stash['schema']) ?
                     $this->stash['schema'] : null;
    }

    /**
     * get schema paths from config.
     *
     * @return array paths
     */
    public function getSchemaPaths()
    {
        return isset($this->stash['schema']['paths'])
                    ? $this->stash['schema']['paths'] : null;
    }

    public function setAutoId($enabled = true)
    {
        $this->stash['schema']['auto_id'] = $enabled;
    }

    public function hasAutoId()
    {
        return isset($this->stash['schema']['auto_id']);
    }


    public function getAutoIdColumnName()
    {
        if (is_array($this->stash['schema']['auto_id'])) {
            if (isset($this->stash['schema']['auto_id']['name'])) {
                return $this->stash['schema']['auto_id']['name'];
            }
        }
        return 'id';
    }

    public function hasAutoIdConfig()
    {
        return is_array($this->stash['schema']['auto_id']);
    }

    public function getAutoIdColumnClass()
    {
        if (is_array($this->stash['schema']['auto_id'])) {
            if (isset($this->stash['schema']['auto_id']['class'])) {
                return $this->stash['schema']['auto_id']['class'];
            }
        }
        // Alternative class '\Maghead\Schema\Column\UUIDPrimaryKeyColumn';
        return self::DEFAULT_AUTO_ID_COLUMN_CLASS;
    }

    public function getAutoIdColumnParams()
    {
        if (is_array($this->stash['schema']['auto_id'])) {
            if (isset($this->stash['schema']['auto_id']['params'])) {
                return $this->stash['schema']['auto_id']['params'];
            }
        }
        // Alternative class '\Maghead\Schema\Column\UUIDPrimaryKeyColumn';
        return self::DEFAULT_AUTO_ID_COLUMN_CLASS;
    }

    public function getBaseModelClass()
    {
        if (isset($this->stash['schema']['base_model'])) {
            return $this->stash['schema']['base_model'];
        }

        return self::DEFAULT_BASE_MODEL_CLASS;
    }

    public function getBaseCollectionClass()
    {
        if (isset($this->stash['schema']['base_collection'])) {
            return $this->stash['schema']['base_collection'];
        }

        return self::DEFAULT_BASE_COLLECTION_CLASS;
    }

    public function & getStash()
    {
        return $this->stash;
    }

    public function setStash(array $config)
    {
        $this->stash = $config;
    }



    /******************************
     * Implements interface of ArrayAccess
     ******************************/
    public function & offsetGet($offset)
    {
        return $this->stash[ $offset ];
    }

    public function offsetSet($offset, $value)
    {
        $this->stash[ $offset ] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->stash[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->stash[$offset]);
    }
}
