<?php

namespace App\Repositories;

use App\Models\BaseballGame;
use App\Models\BaseballPractice;
use Carbon\Carbon;
use Exception;

class BaseballRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return BaseballPractice
     */
    public static function findOnePractice($where, $with = [])
    {
        return BaseballPractice::with($with)->where($where)->first();
    }

     /**
      * Find all
      *
      * @param  array  $where
      * @param  array  $with
      * @return BaseballPractice
      */
     public static function findAll($where, $with = [])
     {
         return BaseballPractice::with($with)->where($where)->get();
     }

    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return BaseballGame
     */
    public static function findOneGame($where, $with = [])
    {
        return BaseballGame::with($with)->where($where)->first();
    }

     /**
      * Find all
      *
      * @param  array  $where
      * @param  array  $with
      * @return BaseballGame
      */
     public static function findAllGame($where, $with = [])
     {
         return BaseballGame::with($with)->where($where)->get();
     }

    /**
     * Load record list for admin
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadPracticeList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = $request->perPage ?? 8;
            $list = BaseballPractice::where('status', '!=', 'deleted');

            if (! empty($post['search'])) {
                // $list->where('game_name', 'like', '%'.$post['search'].'%');
                // $list->where('h_hitting_type', $post['search']);
                $list->where(function ($query) use ($post) {
                    $query->where('game_name', 'like', '%'.$post['search'].'%')
                          ->orWhere('h_hitting_type', 'like', '%'.$post['search'].'%');
                });
                // dd($post['search'],$list);
            }

            //Search from date
            if (! empty($post['start_date']) && ! empty($post['end_date'])) {
                $startDate = $post['start_date'].' 00:00:00';
                $endDate = $post['end_date'].' 23:59:00';
                $list->where('date', '>=', $startDate)->where('date', '<=', $endDate);
            }

            //Search from status
            if (! empty($post['status'])) {
                $list->where('status', $post['status']);
            }
            //Search from type
            if (! empty($post['game_name'])) {
                $list->where('game_name', $post['game_name']);
            }
            //Sort by
            if (! empty($post['sort_by']) && ! empty($post['sort_order'])) {
                $sortBy = $post['sort_by'];
                $sortOrder = $post['sort_order'];
            }
            $list = $list->orderBy($sortBy, $sortOrder);
            $list = $list->paginate($paginationLimit);

            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Change record status by Id
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function changePracticeStatus($request)
    {
        try {
            $model = BaseballPractice::where(['id' => $request->id])->first();
            if (! empty($model)) {
                $model->status = $request->status;
                $model->save();

                return true;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Add Record
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function savePractice($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = new BaseballPractice();
            $model->date = normalizeDate($post['date'] ?? null);
            $model->game_name = ! empty($post['game_name']) ? $post['game_name'] : null;
            $model->p_pitches = ! empty($post['p_pitches']) ? $post['p_pitches'] : 0;
            $model->p_strikes = ! empty($post['p_strikes']) ? $post['p_strikes'] : 0;
            $model->p_balls = ! empty($post['p_balls']) ? $post['p_balls'] : 0;
            $model->p_pitching_session = ! empty($post['p_pitching_session']) ? $post['p_pitching_session'] : null;
            $model->p_fastball_speed = ! empty($post['p_fastball_speed']) ? $post['p_fastball_speed'] : 0;
            $model->p_changeup_speed = ! empty($post['p_changeup_speed']) ? $post['p_changeup_speed'] : 0;
            $model->p_curveball_speed = ! empty($post['p_curveball_speed']) ? $post['p_curveball_speed'] : 0;
            $model->p_pt_curveball = ! empty($post['p_pt_curveball']) ? $post['p_pt_curveball'] : null;
            $model->p_pt_fastball = ! empty($post['p_pt_fastball']) ? $post['p_pt_fastball'] : null;
            $model->p_pt_changeup = ! empty($post['p_pt_changeup']) ? $post['p_pt_changeup'] : null;
            $model->p_pt_other_pitch = ! empty($post['p_pt_other_pitch']) ? $post['p_pt_other_pitch'] : null;

            $model->h_number_of_swings = ! empty($post['h_number_of_swings']) ? $post['h_number_of_swings'] : 0;
            $model->h_hitting_type = ! empty($post['h_hitting_type']) ? $post['h_hitting_type'] : 0;
            $model->h_bat_speed = ! empty($post['h_bat_speed']) ? $post['h_bat_speed'] : 0;

            $model->f_number_of_ground_balls = ! empty($post['f_number_of_ground_balls']) ? $post['f_number_of_ground_balls'] : 0;
            $model->f_number_of_fly_balls = ! empty($post['f_number_of_fly_balls']) ? $post['f_number_of_fly_balls'] : 0;

            $model->created_by = $userData->id;
            $model->updated_by = $userData->id;
            $model->created_at = $currentDateTime;
            $model->updated_at = $currentDateTime;
            $model->save();

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Update Record
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function updatePractice($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = self::findOnePractice(['id' => $request->id]);

            if (! empty($model)) {
                $model->date = normalizeDate($post['date'] ?? null);
                // $model->game_name = ! empty($post['game_name']) ? $post['game_name'] : null;
                $model->p_pitches = ! empty($post['p_pitches']) ? $post['p_pitches'] : 0;
                $model->p_strikes = ! empty($post['p_strikes']) ? $post['p_strikes'] : 0;
                $model->p_balls = ! empty($post['p_balls']) ? $post['p_balls'] : 0;
                $model->p_pitching_session = ! empty($post['p_pitching_session']) ? $post['p_pitching_session'] : null;
                $model->p_fastball_speed = ! empty($post['p_fastball_speed']) ? $post['p_fastball_speed'] : 0;
                $model->p_changeup_speed = ! empty($post['p_changeup_speed']) ? $post['p_changeup_speed'] : 0;
                $model->p_curveball_speed = ! empty($post['p_curveball_speed']) ? $post['p_curveball_speed'] : 0;
                $model->p_pt_curveball = ! empty($post['p_pt_curveball']) ? $post['p_pt_curveball'] : null;
                $model->p_pt_fastball = ! empty($post['p_pt_fastball']) ? $post['p_pt_fastball'] : null;
                $model->p_pt_changeup = ! empty($post['p_pt_changeup']) ? $post['p_pt_changeup'] : null;
                $model->p_pt_other_pitch = ! empty($post['p_pt_other_pitch']) ? $post['p_pt_other_pitch'] : null;

                $model->h_number_of_swings = ! empty($post['h_number_of_swings']) ? $post['h_number_of_swings'] : 0;
                $model->h_hitting_type = ! empty($post['h_hitting_type']) ? $post['h_hitting_type'] : 0;
                $model->h_bat_speed = ! empty($post['h_bat_speed']) ? $post['h_bat_speed'] : 0;

                $model->f_number_of_ground_balls = ! empty($post['f_number_of_ground_balls']) ? $post['f_number_of_ground_balls'] : 0;
                $model->f_number_of_fly_balls = ! empty($post['f_number_of_fly_balls']) ? $post['f_number_of_fly_balls'] : 0;

                $model->updated_by = $userData->id;
                $model->updated_at = $currentDateTime;
                $model->save();

                return true;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Load record list for admin
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadGameList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = $post['perPage'] ?? 8;
            $list = BaseballGame::where('status', '!=', 'deleted');

            if (! empty($post['search'])) {
                $list->where('date', 'like', '%'.$post['search'].'%');
            }
            //Search from status
            if (! empty($post['status'])) {
                $list->where('status', $post['status']);
            }
            //Search from date
            if (! empty($post['start_date']) && ! empty($post['end_date'])) {
                $startDate = $post['start_date'].' 00:00:00';
                $endDate = $post['end_date'].' 23:59:00';
                $list->where('date', '>=', $startDate)->where('date', '<=', $endDate);
            }
            //Sort by
            if (! empty($post['sort_by']) && ! empty($post['sort_order'])) {
                $sortBy = $post['sort_by'];
                $sortOrder = $post['sort_order'];
            }
            $list = $list->orderBy($sortBy, $sortOrder);
            $list = $list->paginate($paginationLimit);

            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Change record status by Id
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function changeGameStatus($request)
    {
        try {
            $model = BaseballGame::where(['id' => $request->id])->first();
            if (! empty($model)) {
                $model->status = $request->status;
                $model->save();

                return true;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function saveGame($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = new BaseballGame();
            $model->date = normalizeDate($post['date'] ?? null);
            // dd('my game', $post['date'] ,  date('Y-m-d', strtotime($post['date'])));
            $model->game_name = ! empty($post['game_name']) ? $post['game_name'] : null;
            $model->p_pitches = ! empty($post['p_pitches']) ? $post['p_pitches'] : 0;
            $model->p_strikes = ! empty($post['p_strikes']) ? $post['p_strikes'] : 0;
            $model->p_balls = ! empty($post['p_balls']) ? $post['p_balls'] : 0;
            $model->p_innings = ! empty($post['p_innings']) ? $post['p_innings'] : 0;
            $model->p_hits = ! empty($post['p_hits']) ? $post['p_hits'] : 0;
            $model->p_runs = ! empty($post['p_runs']) ? $post['p_runs'] : 0;
            $model->p_walks = ! empty($post['p_walks']) ? $post['p_walks'] : 0;
            $model->p_hbp = ! empty($post['p_hbp']) ? $post['p_hbp'] : 0;

            $model->h_plate_attempts = ! empty($post['h_plate_attempts']) ? $post['h_plate_attempts'] : 0;
            $model->h_hits = ! empty($post['h_hits']) ? $post['h_hits'] : 0;
            $model->h_walks = ! empty($post['h_walks']) ? $post['h_walks'] : 0;
            $model->h_rbi = ! empty($post['h_rbi']) ? $post['h_rbi'] : 0;

            $model->f_number_of_attempts = ! empty($post['f_number_of_attempts']) ? $post['f_number_of_attempts'] : 0;
            $model->f_errors = ! empty($post['f_errors']) ? $post['f_errors'] : 0;
            $model->f_outs_made = ! empty($post['f_outs_made']) ? $post['f_outs_made'] : 0;

            $model->created_by = $userData->id;
            $model->updated_by = $userData->id;
            $model->created_at = $currentDateTime;
            $model->updated_at = $currentDateTime;
            $model->save();

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Update Record
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function updateGame($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = self::findOneGame(['id' => $request->id]);

            if (! empty($model)) {
                $model->date = normalizeDate($post['date'] ?? null);
                $model->game_name = ! empty($post['game_name']) ? $post['game_name'] : null;
                $model->p_pitches = ! empty($post['p_pitches']) ? $post['p_pitches'] : 0;
                $model->p_strikes = ! empty($post['p_strikes']) ? $post['p_strikes'] : 0;
                $model->p_balls = ! empty($post['p_balls']) ? $post['p_balls'] : 0;
                $model->p_innings = ! empty($post['p_innings']) ? $post['p_innings'] : 0;
                $model->p_hits = ! empty($post['p_hits']) ? $post['p_hits'] : 0;
                $model->p_runs = ! empty($post['p_runs']) ? $post['p_runs'] : 0;
                $model->p_walks = ! empty($post['p_walks']) ? $post['p_walks'] : 0;
                $model->p_hbp = ! empty($post['p_hbp']) ? $post['p_hbp'] : 0;

                $model->h_plate_attempts = ! empty($post['h_plate_attempts']) ? $post['h_plate_attempts'] : 0;
                $model->h_hits = ! empty($post['h_hits']) ? $post['h_hits'] : 0;
                $model->h_walks = ! empty($post['h_walks']) ? $post['h_walks'] : 0;
                $model->h_rbi = ! empty($post['h_rbi']) ? $post['h_rbi'] : 0;

                $model->f_number_of_attempts = ! empty($post['f_number_of_attempts']) ? $post['f_number_of_attempts'] : 0;
                $model->f_errors = ! empty($post['f_errors']) ? $post['f_errors'] : 0;
                $model->f_outs_made = ! empty($post['f_outs_made']) ? $post['f_outs_made'] : 0;

                $model->updated_by = $userData->id;
                $model->updated_at = $currentDateTime;
                $model->save();

                return $model;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
