<?php

namespace App\Repositories;

use App\Models\Quote;
use App\Models\QuoteView;
use App\Models\User;
use Config;
use Exception;

class QuoteRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  Quote
     */
    public static function findOne($where, $with = [])
    {
        return Quote::with($with)->where($where)->first();
    }

    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  Quote
     */
    public static function getLastQuote()
    {
        $userData = getUser();

        return Quote::where('status', '!=', 'deleted')->first();
    }

    /**
     * Find all
     *
     * @param  array  $where
     * @param  array  $with
     * @return  Quote
     */
    public static function findAll($where, $with = [])
    {
        return Quote::with($with)->where($where)->get();
    }

    /**
     * Load record list for admin
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadQuoteList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = Quote::where('status', '!=', 'deleted');
            // if ($userData->user_type != 'admin') {
            //     $list->where('created_by', $userData->id);
            // }
            //Search from name
            if (! empty($post['search'])) {
                $list->where('description', 'like', '%'.$post['search'].'%');
            }
            //Search from status
            if (! empty($post['status'])) {
                $list->where('status', $post['status']);
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
    public static function changeStatus($request)
    {
        try {
            $model = Quote::where(['id' => $request->id])->first();
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
    public static function save($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = new Quote();
            $model->author = ! empty($post['author']) ? $post['author'] : '';
            $model->quote_type = ! empty($post['quote_type']) ? $post['quote_type'] : '';
            $model->description = ! empty($post['description']) ? $post['description'] : '';
            //$model->is_quote = ! empty($post['is_quote']) ? $post['is_quote'] : 0;
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
    public static function update($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = self::findOne(['id' => $request->id]);
            if (! empty($model)) {
                $model->author = ! empty($post['author']) ? $post['author'] : '';
                $model->quote_type = ! empty($post['quote_type']) ? $post['quote_type'] : '';
                $model->description = ! empty($post['description']) ? $post['description'] : '';
                //$model->is_quote = ! empty($post['is_quote']) ? $post['is_quote'] : 0;
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

    public static function resetUserQuote($user)
    {
        try {
            User::where('id', $user->id)->update(['quote_id' => null]);

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function setUserQuote($user)
    {
        try {
            $quote = '';
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $userData = User::where('id', $user->id)->first();
            $viewQuote = QuoteView::where('user_id', $userData->id)->first();
            if (! empty($viewQuote) && ! empty($viewQuote->viewed_quote_ids)) {
                $quoteIds = explode(',', $viewQuote->viewed_quote_ids);
                $quote = Quote::where('status', 'active')->whereNotIn('id', $quoteIds)->first();
            }
            if (empty($quote)) {
                $quote = Quote::where('status', 'active')->first();
            }
            if (! empty($quote)) {
                $userData->quote_id = $quote->id;
                $userData->save();
                if (empty($viewQuote)) {
                    $viewQuote = new QuoteView();
                    $viewQuote->viewed_quote_ids = $quote->id;
                    $viewQuote->user_id = $userData->id;
                    $viewQuote->created_at = $currentDateTime;
                    $viewQuote->updated_at = $currentDateTime;
                    $viewQuote->save();
                } else {
                    $viewedQuoteIds = ! empty($viewQuote->viewed_quote_ids) ? explode(',', $viewQuote->viewed_quote_ids) : [];
                    if (in_array($quote->id, $viewedQuoteIds)) {
                        $viewedQuoteIds = [];
                    }
                    array_push($viewedQuoteIds, $quote->id);
                    $viewQuote->viewed_quote_ids = implode(',', $viewedQuoteIds); //$viewQuote->viewed_quote_ids.','.$quote->id;
                    $viewQuote->save();
                }
            }

            return $quote;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
