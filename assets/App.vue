<template>
  <div class="app">
    <vue-snotify></vue-snotify>
    <div class="row py-5 text-center justify-content-center">
      <h2>Import CSV file</h2>
      <p class="lead">Example task for import</p>
      <div class="col-4">
        <div class="card p-2">
          <div class="form-group m-2">
            <input type="file" id="file" ref="file" accept=".csv" v-on:change="handleFileUpload()"/>
            <button :disabled="disabledButton" class="btn btn-primary" v-on:click="submitFile()">Submit</button>
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
    importId: '',
    importStatus: 0,
    errors: [],
    importError: '',
    disabledButton: true
  }),
  created() {

  },
  methods: {
    notification() {
      this.$snotify.async('Import file', 'Success async', () => new Promise((resolve, reject) => {
        let interval = setInterval(() => {
          this.getResult()
          this.getError()
          if (this.importStatus === 2) {
            clearInterval(interval);
            return resolve({
              title: 'Success!',
              body: 'File successfully imported!',
              config: {
                closeOnClick: true
              }
            })
          } else if (this.importStatus === 1) {
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
      this.importStatus = 0
      this.importError = ''
      let formData = new FormData();
      formData.append('file', this.file);
      try {
        let response = await this.axios.post('/upload',
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
          timeout: 2000,
          pauseOnHover: true
        });
      }
    },
    handleFileUpload() {
      this.file = this.$refs.file.files[0];
      if(!this.file || this.file.type !== 'text/csv'){
        this.$snotify.error('Bad file', 'Upload error', {
          timeout: 2000,
          pauseOnHover: true
        });
      } else {
        this.disabledButton = false
      }
    },
    async getResult() {
      try {
        let response = await this.axios.get('/import/result/' + this.importId)
        this.importStatus = response.data
      } catch (err) {
        this.errors = err
        console.log(err)
      }
    },
    getError() {
      try {
        this.axios.get('/import/errors/' + this.importId)
        .then((response) => {
          this.importError = response.data
        })
        return this.importError
      } catch (err) {
        console.log(err)
      }
    }
    // getFilename() {
    //   return this.file.
    // }
  }
}
</script>

<style scoped>

</style>