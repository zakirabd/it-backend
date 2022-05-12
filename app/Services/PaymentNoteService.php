<?php

namespace App\Services;
use App\PaymentNote;
use Carbon\Carbon;

class PaymentNoteService
{
    private $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function create()
    {
        $today = carbon::parse($this->request->date)->format('Y-m-d');

        PaymentNote::create([
            'title' => $this->request->note,
            'date' => $today,
            'student_id' => $this->request->student_id,
            'created_at' => Carbon::now(),
        ]);

    }

}
