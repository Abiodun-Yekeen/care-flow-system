<?php
//namespace App\Modules\OfficeFiles\Registry\Services;
//use App\Modules\OfficeFiles\Registry\DTO\RegistryDTO;
//use App\Modules\OfficeFiles\Registry\Models\FileReceive;
//use App\Modules\OfficeFiles\Registry\Services\Contracts\RegistryInterface;
//use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Storage;
//
//class RegistryService
//{
//
//    public function __construct(
//        private RegistryInterface $registry
//    ) {}
//
//    public function submit(RegistryDTO $dto)
//    {
//        return DB::transaction(function () use ($dto) {
//
//            $fileReceive = $this->registry->create(
//                'file_id',
//                'receive_department_id',
//                'created_by',
//                'received_from',
//                'remark'=>$dto->remark,
//                'status',
//                'date_received'=>$dto->date_received,
//                'submitted_at'=> now(),
//            );
//$fileReceive->file()->create(
//    'uuid',
//    'official_file_number',
//    'temp_file_number',
//    'is_temporary',
//    'subject',
//    'description',
//    'source_name',
//    'source_reference_no',
//    'status',
//    'priority',
//    'current_department_id',
//    'current_holder_user_id',
//    'created_by',
//    'registered_by',
//    'date_received',
//    'last_movement_at',
//    'closed_at',
//);
//
//if($dto->file_path){
//    $imageData = $dto->file_path;
//    foreach ($imageData as $image) {
//        $fileName =  $patient->uuid . '_' . time() . '.jpg';
//        // Clean the Base64 string
//        $base64Image = explode(',', $image)[1];
//        // Store the physical file
//        Storage::disk('public')->put('patients/photos/' . $fileName, base64_decode($base64Image));
//        $fileReceive->document()->create([
//            'file_path' => $fileName,
//        ]);
//    }
//
//}
//$fileReceive->file->movement->create([
//    'file_id',
//    'from_department_id',
//    'from_user_id',
//    'to_department_id',
//    'to_user_id',
//    'acted_by_user_id',
//    'received_by_user_id',
//    'movement_type',
//    'movement_status',
//    'remarks',
//    'minute',
//    'acted_at',
//    'received_at',
//])
//
//
//        });
//    }
//}
