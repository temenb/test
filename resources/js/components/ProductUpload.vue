

<template>
    <div>
        <div v-if="message != ''">
            {{message}}
        </div>
        <div v-if="success != ''">
            {{success}}
        </div>

        <form @submit="formSubmit" enctype="multipart/form-data">
            <strong>File:</strong>
            <input type="file" v-on:change="onFileChange" />

            <button>Upload</button>
        </form>
    </div>
</template>

<script>
export default {
    data() {
        return {
            file: '',
            success: '',
            message: ''
        };
    },
    methods: {
        onFileChange(e){
            this.file = e.target.files[0];
        },
        formSubmit(e) {
            e.preventDefault();
            const self = this;
            self.message = '';

            const url = '/product/upload';
            const config = {
                headers: { 'content-type': 'multipart/form-data' }
            }

            let formData = new FormData();
            formData.append('file', this.file);


            axios.post(url, formData, config)
                .then(function (response) {
                    self.success = response.data.success;
                    if (response.data.not_updated) {
                        self.message = 'Following products were not updated: ' +
                            JSON.stringify(response.data.not_updated);
                        console.log(response.data.not_updated);
                    }
                })
                .catch(function (error) {
                    console.log(error);
                    if (error.response && error.response.data && error.response.data.message ) {
                        self.message = error.response.data.message;
                    }
                });
        }
    }
}
</script>
