<?php

namespace App\Exports;
use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromCollection, WithMapping, WithHeadings, WithStyles
{

    private $filters = null;

    public function __construct($request)
    {
        $this->filters = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->filters->company_id) {
            return $user = User::with('studentEnrollClass:id,student_id,lesson_mode')->where('role', 'student')->where('company_id', $this->filters->company_id)->latest('id')->get();
        } else {
            return $user = User::with('studentEnrollClass:id,student_id,lesson_mode')->where('role', 'student')->latest('id')->get();
        }
    }

    /**
     * @param mixed $user
     * @return array
     */
    public function map($user): array
    {
        $items = [];
        foreach ($user->studentEnrollClass as $class) {
            array_push($items, $class->lesson_mode);
        }
        return [
            $user->first_name,
            $user->last_name,
            $user->email,
            $user->phone_number,
            $user->full_name,
//            implode(', ', array_unique($items))
            implode(', ', $items)
        ];
    }

    /**
     * @return array|string[]
     */
    public function headings(): array
    {
        return ['First Name', 'Last Name', 'Email', 'Phone Number', 'Full Name', 'Classes'];
    }

    /**
     * @param Worksheet $sheet
     * @return \bool[][][]
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

}
