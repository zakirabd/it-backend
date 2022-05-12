<?php

namespace App\Listeners;
use App\Attendance;
use App\User;
class StudentAttendanceUnlockCheckListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {

        $attendance_count = Attendance::where('user_id',$event->student_id)->where('paid', 0)->get();
        $student = User::findOrFail($event->student_id);
        $classChecked = false;
        foreach ($attendance_count->groupBy('teacher_id') as $class) {
            $classChecked = $class->count() >= $student->payment_Reminder_max_value;
            if ($classChecked === true) break;
        }
        if($classChecked == false){
//            error_log('status'. $classChecked);
            User::findOrFail($event->student_id)->update([
                'attendance_lock_status'=>0
            ]);

        }
    }
}
