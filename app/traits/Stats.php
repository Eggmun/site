<?php

class Stats extends BaseController {

	use Player;

	/*
	|--------------------------------------------------------------------------
	| Stats Controller
	|--------------------------------------------------------------------------
	|
	| Generates various different stats.
	| Sadly I have to touch HTML in a controller. It's just echoed out.
	| Google's graph API will come in stupidly handy here.
	|
	|	Route::get('/stats/graph', 'StatsController@showGraphs');
	|	Route::get('/stats/graph.json', StatsController@getGraphsAjax');
	|	Route::get('/stats/djs', 'StatsController@showDJStats');
	|	Route::get('/stats/faves', 'StatsController@showFaveStats');
	|
	*/

	protected $layout = 'master';

	/**
	 * Fetches arrays for use in the Google Graph JS API
	 * Interval is default 288 rows - 24 hours (5min increments)
	 *
	 * @param int $interval
	 * @return array
	 */
	protected function getGraphs($interval = 288) {
		// we need a plain array here
		DB::setFetchMode(PDO::FETCH_NUM);

		$stats = DB::table('listenlog')
			->select(
				'listenlog.time',
				'listenlog.listeners',
				'djs.djname'
			)
			->join('djs', 'listenlog.dj', '=', 'djs.id')
			->orderBy('listenlog.id', 'desc')
			->take($interval)
			->get();

		// post-processing is mandatory because mysql is fucking stupid for JS

		foreach ($stats as &$stat) {
			$stat[0] = DateTime::createFromFormat('Y-m-d H:i:s', $stat[0])->format('Y,m,d,H,i,s');
			$stat[1] = (int) $stat[1];
			$stat[3] = ($stat[2] == "Hanyuu-sama") ? FALSE : TRUE;

			$stat[2] = "<div style=\"width: 120px; padding: 6px;\"><p><b>DJ:</b> {$stat[2]}</p><p><b>Listeners:</b> {$stat[1]}</p></div>";
		}


		// return fetch mode to normal
		DB::setFetchMode(Config::get('database.fetch', PDO::FETCH_ASSOC));
		return $stats;

	}

	/**
	 * Fetches DJ stats.
	 *
	 * @return string
	 */
	protected function getFaveStats() {

	}

	/**
	 * Fetches the HTML for DJ stats
	 *
	 * @return string
	 */
	protected function getDJStats() {

	}

	/**
	 * Show the Graphs HTML.
	 *
	 * @return string
	 */
	protected function getGraphStats() {

	}

}
