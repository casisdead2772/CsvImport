<template>
  <div class="app">
    <vue-snotify></vue-snotify>
    <div class="row py-5 text-center justify-content-center">
      <h2>Import CSV file</h2>
      <p class="lead">Example task for import</p>
      <div class="col-4">
        <div class="card p-2">
          <div class="form-group m-2">
            <input type="file" id="file" ref="file" v-on:change="handleFileUpload()"/>
            <button class="btn btn-primary" v-on:click="submitFile()">Submit</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  name: "App.vue",
  data: () => ({
    file: '',
    result: '',
    importId: '',
    importStatus: 0,
    errors: [],
    importError: ''
  }),
  created() {

  },
  methods: {
    notification() {
      this.$snotify.async('Import file', 'Success async', () => new Promise((resolve, reject) => {
        let interval = setInterval(() => {
          this.getResult()
          this.getError()
          if (this.importStatus == 2) {
            clearInterval(interval);
            return resolve({
              title: 'Success!',
              body: 'File successfully imported!',
              config: {
                closeOnClick: true
              }
            })
          } else if (this.importStatus == 1) {
            clearInterval(interval);
            return reject({
              title: 'Error!',
              body: this.importError,
              config: {
                closeOnClick: true
              }
            })
          }
        }, 2000);
      }));
    },
    async submitFile() {
      let formData = new FormData();
      formData.append('file', this.file);
      try {
        let response = await this.axios.post('/',
            formData,
            {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }
        )
        this.importId = response.data
        this.notification()
      } catch (err) {
        this.errors = err.response.data.detail

        this.$snotify.error(this.errors, 'Upload error', {
          timeout: 5000,
          pauseOnHover: true
        });
      }
    },
    handleFileUpload() {
      this.file = this.$refs.file.files[0];
    },
    async getResult() {
      try {
        let response = await this.axios.get('/result/' + this.importId)
        this.importStatus = response.data
      } catch (err) {
        this.errors = err
        console.log(err)
      }
    },
    async getError() {
      try {
        let response = await this.axios.get('/errors/' + this.importId)
        this.importError = response.data
      } catch (err) {
        console.log(err)
      }
    }
  }
}
</script>

<style scoped>

</style>