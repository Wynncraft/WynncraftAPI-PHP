<?php
/**
 * A Wynncraft API interface for PHP
 *
 * @author Chris Ireland <ireland63@gmail.com>
 * @license MIT <http://opensource.org/licenses/MIT>
 */
class wynncraftAPI
{

    /**
     * Url to the api
     *
     * @var string
     */
    private $apiUrl = 'http://wynncraft.com/api/public_api.php?';

    /**
     * The format the api call should be returned as
     *
     * @var string
     */
    private $apiFormat = 'string';

    /**
     * Create a new class instance
     *
     * @param string $apiFormat
     * @throws Exception
     */
    function __construct($apiFormat = 'string')
    {
        // Set class variables
        $this->apiFormat = $apiFormat;

        // Validation format option
        if ($this->apiFormat !== 'string' && $this->apiFormat !== 'object' && $this->apiFormat !== 'array' )
            throw new Exception('Invalid API return format');
    }

    /**
     * API command builder and executor
     *
     * @param $action
     * @param null $command
     * @return array|mixed|string
     * @throws Exception
     */
    protected function apiCommand($action, $command = null)
    {
        // Build the http query to the api
        $apiQuery = array(
            'action' => $action,
            'command' => $command,
        );
        $apiQuery = http_build_query($apiQuery);

        // Query the api and fetch the response
        $ch = curl_init($this->apiUrl . $apiQuery);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $apiQuery = curl_exec($ch);

        if($apiQuery === false || curl_getinfo($ch,CURLINFO_HTTP_CODE) !== 200)
            throw new Exception('Wynncraft API failed to respond correctly');

        curl_close($ch);

        // Handle formatting
        if ($this->apiFormat === 'object') {
            $apiQuery = json_decode($apiQuery);

        } elseif ($this->apiFormat === 'array') {
            $apiQuery = json_decode($apiQuery, true);

        }

        return $apiQuery;
    }

    /**
     * Return a JSON document with a player's stats
     *
     * @param $username
     * @return array|mixed|string
     * @throws Exception
     */
    public function playerStats($username) {
        return $this->apiCommand('playerStats', $username);
    }

    /**
     * Return a JSON document detailing all servers and their players
     *
     * @return array|mixed|string
     * @throws Exception
     */
    public function onlinePlayers() {
        return $this->apiCommand('onlinePlayers');
    }

    /**
     * Return a JSON document will a sum of all online players
     *
     * @return array|mixed|string
     * @throws Exception
     */
    public function onlinePlayersSum() {
        return $this->apiCommand('onlinePlayersSum');
    }

    /**
     * Return a JSON document with pvp statistic data
     *
     * @param null $type
     * @return array|mixed|string
     * @throws Exception
     */
    public function pvpLeaderboard($type = null) {
        return $this->apiCommand('pvpLeaderboard', $type);
    }

}
