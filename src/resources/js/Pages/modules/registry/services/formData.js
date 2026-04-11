

// Initial Form State
export const formData = {
    title:'',
    first_name:'',
    middle_name:'',
    surname:'',
    gender:'',
    date_of_birth:'',
    marital_status:'',
    phone_number:'',
    address:'',
    state:'',
    lga:'',
    blood_group:'',
    genotype:'',
    next_of_kin_name:'',
    next_of_kin_phone:'',
    next_of_kin_relationship:'',
    photo:null
}


export // Section Definitions & Required Fields
const sections = {
    demographics: {
        label: 'Demographics',
        icon: 'User',
        required: ['first_name', 'surname', 'gender', 'date_of_birth','marital_status']
    },
    contact: {
        label: 'Contact',
        icon: 'Phone',
        required: ['phone_number', 'address']
    },
    clinical: {
        label: 'Clinical',
        icon: 'Heart',
        required: []
    },
    nextOfKin: {
        label: 'Next of Kin',
        icon: 'Users',
        required: ['next_of_kin_name', 'next_of_kin_phone']
    }
}
