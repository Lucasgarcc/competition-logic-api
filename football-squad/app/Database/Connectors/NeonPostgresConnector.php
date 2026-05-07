<?php

namespace App\Database\Connectors;

use Illuminate\Database\Connectors\PostgresConnector;

class NeonPostgresConnector extends PostgresConnector
{
    /**
     * Add Neon-compatible SSL and connection options to the DSN.
     */
    protected function addSslOptions($dsn, array $config)
    {
        $dsn = parent::addSslOptions($dsn, $config);

        if (! empty($config['channel_binding'])) {
            $dsn .= ";channel_binding={$config['channel_binding']}";
        }

        if (! empty($config['connect_options'])) {
            $dsn .= ";options={$config['connect_options']}";
        }

        return $dsn;
    }
}
