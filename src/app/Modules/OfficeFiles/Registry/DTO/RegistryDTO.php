<?php
namespace App\Modules\OfficeFiles\Registry\DTO;
class RegistryDTO
{

    public function __construct(
        public readonly string $temp_file_number,
        public readonly ?int $file_id,
        public readonly ?int $receive_department_id,
        public readonly ?int $created_by,
        public readonly ?string $received_from,
        public readonly string $status,
        public readonly string $submitted_at,
        public readonly ?string $remark,
        public readonly string $source_name,
        public readonly string $subject,
        public readonly string $source_reference_no,
        public readonly string $date_received,
        public readonly string $file_path,

    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            temp_file_number: '',
            file_id: null,
            receive_department_id: null,
            created_by: null,
            received_from: '',
            status: '',
            submitted_at: '',

            remark: $data['remark'],
            source_name: $data['source_name'],
            subject: $data['subject'],
            source_reference_no: $data['source_reference_no'],
            date_received: $data['date_received'],
            file_path: $data['scanned_file'],
        );
    }
}
