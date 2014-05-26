<?php
/**
 * A Wynncraft API interface for PHP
 * 
 * - Docs available for the API @ <http://docs.wynncraft.com/>
 *
 * @author Chris Ireland <ireland63@gmail.com>
 * @license MIT <http://opensource.org/licenses/MIT>
 */
class wynncraftAPI
{

    /**
     * Url to the api
     *
     * @const string
     */
    const apiUrl = 'http://api.wynncraft.com/public_api.php?';

    /**
     * The format the api call should be returned as
     *
     * @var string
     */
    private $apiFormat = 'string';

    /**
     * Create a new class instance
     *  - There are 3 available $apiFormats
     *    - 'string'|null : returns as a json document (default)
     *    - 'array' : returns as a php array
     *    - 'object' : returns as as a php object
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
     * @param null $limit
     * @return array|mixed|string
     * @throws Exception
     */
    protected function apiCommand($action, $command = null, $limit = null)
    {
        // Build the http query to the api
        $apiQuery = array(
            'action' => $action,
            'command' => $command,
            'limit' => $limit
        );
        $apiQuery = http_build_query($apiQuery);

        // Query the api and fetch the response
        $ch = curl_init(self::apiUrl . $apiQuery);
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
     * Return a JSON document with a sum of all online players
     *
     * @return array|mixed|string
     * @throws Exception
     */
    public function onlinePlayersSum() {
        return $this->apiCommand('onlinePlayersSum');
    }

    /**
     * Return a JSON document with pvp statistic data (max limit 100)
     *  - There are 3 $type options:
     *    - 'global'|null : returns all time stats (default)
     *    - 'daily' : returns today's stats
     *    - 'weekly' : returns this week's stats
     *
     * @param null $type
     * @param null $limit
     * @return array|mixed|string
     * @throws Exception
     */
    public function pvpLeaderboard($type = null, $limit = null) {
        return $this->apiCommand('pvpLeaderboard', $type, $limit);
    }
    
    /**
     * Return a JSON document with items based on a filter
     * - There is a smart $filter:
     *   - An integer will search for min lvl
     *   - An item type will search for all items with that type
     *   - A quality will search search for all items with that quality
     *
     * @param string $filter
     * @return array|mixed|string
     * @throws Exception
     */
    public function searchItems($filter) {
        return $this->apiCommand('items', $filter);
    }

}
