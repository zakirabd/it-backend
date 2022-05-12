<?php

namespace App\Listeners;
use App\Attendance;
use App\Events\StudentAttendanceLockCheckEvent;
use App\User;
class StudentAttendanceLockChecklistener
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
     * @param  StudentAttendanceLockCheckEvent  $event
     * @return void
     */
    public function handle(StudentAttendanceLockCheckEvent $event)
    {

        $attendance_count = Attendance::where('user_id',$event->student_id)->where('paid', 0)->get();
        $student = User::findOrFail($event->student_id);
        $classChecked = false;
        foreach ($attendance_count->groupBy('teacher_id') as $class) {
//            error_log('class count'.$class->count());
            $classChecked = $class->count() >= $student->payment_Reminder_max_value;
            if ($classChecked === true) break;
        }
        if($classChecked == true){
            User::findOrFail($event->student_id)->update([
                'attendance_lock_status'=>1
            ]);

        }
    }
}
