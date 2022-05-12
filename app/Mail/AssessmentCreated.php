<?php



namespace App\Mail;



use App\Assessment;

use App\User;

use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Mail\Mailable;

use Illuminate\Queue\SerializesModels;

use App\Notifications\assessmentNotify;



class AssessmentCreated extends Mailable implements ShouldQueue

{

    use Queueable, SerializesModels;



    /**

     * The Assessment instance.

     *

     * @var Assessment $assessment

     */

    public $assessment;

    public $user;

    public $teacher;

    public $reply_mail;



    /**

     * Create a new message instance.

     *

     * @return void

     */

    public function __construct(Assessment $assessment,User $user,User $teacher)

    {

        $this->assessment = $assessment;

        $this->user = $user;

        $this->teacher = $teacher;

        $company_id = auth()->user()->company_id;

        $company_email = User::whereIn('role',['company_head'])->where('company_id',$company_id)->pluck('email');

        $this->reply_mail=$company_email;







    }



    /**

     * Build the message.

     *

     * @return $this

     */

    public function build()

    {



        return $this

            ->subject('CELT Reporting')

            ->replyTo($this->reply_mail)

            ->markdown('emails.assessments.created')->with('user',$this->user);

    }

}

