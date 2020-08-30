<?php

namespace App\Helpers;

use App\Language;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class Helper
{
    /**
     * Save the translatable fields for each enabled language
     *
     * @param Model $model
     * @param array $fields
     * @param Request $request
     * @return bool
     */
    public static function saveTranslatedFields(Model $model, array $fields, Request $request): bool
    {
        $langs = Language::enable()->get();

        foreach ($langs as $lang) {
            foreach ($fields as $field) {
                $nameField = $field . '_' . $lang->iso;
                $model->setTranslation($field, strtolower($lang->iso), $request->{$nameField});
            }
        }

        $model->save();

        return true;
    }

    /**
     * Save the uploaded files
     *
     * @param array $request
     * @return array
     */
    public static function saveFiles(array $request): array
    {
        foreach ($request as $key => $value) {

            if (Input::hasFile($key)) {
                $destinationPath = public_path() . '/files/';
                $filename = date('YmdHis-') . str_slug($value->getClientOriginalName()) . '.' . $value->getClientOriginalExtension();
                $value->move($destinationPath, $filename);
                $request[$key] = $filename;
            }
        }
        unset($key);
        unset($value);

        return $request;
    }

    /**
     * Translated array of day names
     *
     * @return array
     */
    public static function getDaysArray(): array
    {
        return [
            '1' => trans('days.monday'),
            trans('days.tuesday'),
            trans('days.wednesday'),
            trans('days.thursday'),
            trans('days.friday'),
            trans('days.saturday'),
            trans('days.sunday')
        ];
    }

    /**
     * When a checkbox is unchecked, save it as value 0
     *
     * @param array $request
     * @param array $checkboxes
     * @return array
     */
    public static function saveUncheckedCheckbox(array $request, array $checkboxes): array
    {
        foreach ($checkboxes as $checkbox) {
            if (!isset($request[$checkbox])) {
                $request[$checkbox] = 0;
            }
        }

        return $request;
    }

    /**
     * Parse dates to a Carbon
     *
     * @param array $request
     * @param string $format
     * @param array $dates
     * @return array
     */
    public static function carbonParse(array $request, string $format, array $dates): array
    {
        foreach ($dates as $date) {
            if (isset($request[$date])) {
                $request[$date] = Carbon::createFromFormat($format, $request[$date]);
            }
        }

        return $request;
    }
}
