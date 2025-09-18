<?php

namespace App\Repositories;

use App\Models\Journal;
use Config;
use Exception;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;

class JournalRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  Journal
     */
    public static function findOne($where, $with = [])
    {
        return Journal::with($with)->where($where)->first();
    }

    /**
     * Save Journal
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveJournal($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $journal = new Journal();
            // $journal->title = $request->title;
            $journal->date = normalizeDate($post['date'] ?? null);
            $journal->description = $request->description;
            $journal->user_id = $userData->id;
            $journal->created_by = $userData->id;
            $journal->updated_by = $userData->id;
            $journal->save();

            return $journal;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Save Journal
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function loadJournalList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $date = getTodayDate('Y-m-d');
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = Journal::where('status', '!=', 'deleted')->with('user')->orderBy($sortBy, $sortOrder);
            if (! empty($request->start_date) && ! empty($request->end_date)) {
                $list->where('date', '>=', $request->start_date)->where('date', '<=', $request->end_date);
            } else {
                $list->where('date', '<=', $date);
            }
            if ($userData->user_type != 'admin') {
                $list->where('user_id', $userData->id);
            }
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
            $list = $list->paginate($paginationLimit);
            if (count($list) > 0) {
                foreach ($list as $item) {
                    $description = $item->description;
                    $imagePath = public_path('assets/images/default-notebook.png');
                    $image = Image::make($imagePath);

                    // Define font size and position
                    $fontSize = 100; // Increase font size as needed
                    $xPosition = 50; // Adjust x position to left align
                    $yPosition = 50; // Adjust y position to top align

                    // Add text to the image
                    $image->text($item->description, $xPosition, $yPosition, function ($font) use ($fontSize) {
                        // $font->file(public_path('fonts/arial.ttf')); // Make sure this font file exists if you uncomment this line
                        $font->size($fontSize);
                        $font->color('#000');
                        $font->align('left');
                        $font->valign('top');
                    });

                    // Generate a unique filename using the item's ID or any other unique attribute
                    $customImagePath = public_path('assets/images/custom_image_'.$item->id.'.png');
                    $image->save($customImagePath);

                    // Generate the URL for the saved image
                    $imageUrl = url('assets/images/custom_image_'.$item->id.'.png');
                    $item['custom_image'] = $imageUrl;
                }
            }

            return $list;
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
                $model->date = normalizeDate($post['date'] ?? null);
                $model->description = ! empty($post['description']) ? $post['description'] : '';
                $model->user_id = $userData->id;
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
            $journal = Journal::where(['id' => $request->id])->first();
            if (! empty($journal)) {
                // dd($request->status);
                $journal->status = $request->status;
                $journal->save();

                return true;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Delete status by Id
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function journalDelete($request)
    {
        try {
            $journal = Journal::where(['id' => $request->id])->first();
            if (! empty($journal)) {
                $journal->status = 'deleted';
                $journal->save();

                return true;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
