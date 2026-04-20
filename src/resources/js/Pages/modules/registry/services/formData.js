

// Initial Form State
export const formData = {
  source_name:'',
    subject:'',
    date_received:'',
    remark:'',
    source_reference_no:'',
    scanned_file:[],
    is_draft:false,
    priority:'',
    deadline_at:''
}


export const EditformData=(props) => ({
    source_name: props.file_receive.file.source_name ?? '',
    subject: props.file_receive.file.subject ?? '',
    date_received: props.file_receive.date_received ?? '',
    remark: props.file_receive.remark ?? '',
    source_reference_no: props.file_receive.file.source_reference_no ?? '',
    scanned_file: props.file_receive.file.documents.file_path ?? [],
    is_draft: props.file_receive.status ?? false,
    priority: props.file_receive.file.priority ?? 'medium',
    deadline_at: props.file_receive.deadline_at ?? ''
});

