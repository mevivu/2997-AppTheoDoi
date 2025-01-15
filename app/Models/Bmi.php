<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\User\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bmi extends Model
{
    use HasFactory;

    protected $table = 'bmi_informations';

    protected $fillable = [
        /** Tuổi */
        'age',
        /** Giới tính */
        'gender',
        /** Trạng thái */
        'status',
        /** Z-score -3 */
        'z_score_minus_3',
        /** Z-score -2 */
        'z_score_minus_2',
        /** Z-score -1*/
        'z_score_minus_1',
        /** Z-score 0 */
        'z_score_0',
        /** Z-score +1 */
        'z_score_plus_1',
        /** Z-score +2 */
        'z_score_plus_2',
        /** Z-score +3 */
        'z_score_plus_3'
    ];

    protected $casts = [
        'status' => ActiveStatus::class,
        'gender' => Gender::class,
    ];
}
