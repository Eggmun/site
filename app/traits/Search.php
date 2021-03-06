<?php

trait Search {

	protected function getSearchResults($search) {
		if ($search) {
			if (Cache::section('search')->has($search)) {

				return Cache::section('search')->get($search);

			} else {

				$results = DB::table('tracks')
					->where('usable', '=', 1)
					->where('need_reupload', '=', 0)
					->whereRaw('match (artist, track, album, tags) against (?)', [$search])
					->select("id", "track", "artist", "album", "tags", "lastplayed", "lastrequested", "requestcount", "priority")
					->get();

				$count = 0;

				foreach ($results as &$result) {
					

					$result["break"] = $count;
					if ($count == 2)
						$count = 0;
					else
						$count++;

					$delay = $this->getSongDelay((int) $result["requestcount"]);

					if ((time() - strtotime($result["lastrequested"])) > $delay)
						$result["cooldown"] = false;
					else
						$result["cooldown"] = true;

				}
				Cache::section('search')->put($search, $results, Config::get('cache.times.search'));
				return $results;
			}
		} else {
			return [];
		}

	}

}
