<template>
  <div class="container">
    <div
      class="upload row h-50 align-items-center py-5 text-center justify-content-center"
    >
      <vue-snotify />

      <div class="col-lg-6">
        <div class="card bg-light">
          <h2 class="card-header dark text-white py-3">
            Import CSV file
          </h2>
          <div class="card-body">
            <h3 class="card-title my-3">
              Please select product file
            </h3>
            <div class="input-group px-2 py-4">
              <input
                id="file"
                ref="file"
                type="file"
                class="form-control"
                aria-label="Upload"
                accept=".csv"
                @change="handleFileUpload()"
              >
              <button
                :disabled="disabledButton"
                class="btn btn-primary"
                type="button"
                @click="submitFile()"
              >
                Upload
              </button>
            </div>
            <p class="card-text lead m-3">
              The uploaded file must have a CSV extension of no more than 128mb.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  name: "NotificationComponent",
  data: () => ({
    file: '',
    importId: '',
    importStatus: -1,
    errors: [],
    importError: '',
    disabledButton: true,
    failures: [],
    importStatusMounted: -1,
  }),
  watch: {
    importStatusMounted: function (newStatus) {
      if (parseInt(localStorage.importStatus) !== newStatus) {
        this.notification()
      }
    }
  },
  mounted() {
    if (localStorage.importId) {
      this.importId = localStorage.importId
      this.getResult(true)
    }
  },
  methods: {
    notification() {
      localStorage.importStatus = -1
      this.$snotify.async('Import file', 'In progress', () => new Promise((resolve, reject) => {
        let interval = setInterval(() => {
          this.getResult()
          if (this.importStatus === 2) {
            this.getIncorrectErrors()
            clearInterval(interval);
            localStorage.importStatus = 2
            return resolve({
              title: 'Success!',
              body: 'File successfully imported!',
              config: {
                closeOnClick: true
              }
            })
          } else if (this.importStatus === 1) {
            clearInterval(interval);
            localStorage.importStatus = 1
            return this.getError(reject)
          }
        }, 300);
      }));
    },
    async submitFile() {
      this.$refs.file.value = ''
      this.disabledButton = true
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
        localStorage.importId = response.data
        this.importId = response.data
        this.file = ''
        this.importStatus = -1
        this.notification()
      } catch (err) {
        this.errors = err.response.data.detail
        this.$snotify.error(this.errors, 'Upload error', {
          timeout: 20000,
          pauseOnHover: true
        });
      }
    },

    handleFileUpload() {
      this.file = this.$refs.file.files[0];
      if (!this.file || this.file.type !== 'text/csv') {
        this.$snotify.error('Bad file', 'Upload error', {
          timeout: 20000,
          pauseOnHover: true
        });
        this.$refs.file.value = '';
        this.file = ''
      } else {
        this.disabledButton = false
      }
    },

    async getResult(mount) {
      try {
        let response = await this.axios.get('/import/result/' + this.importId)
        if (mount) {
          this.importStatusMounted = response.data
        } else {
          this.importStatus = response.data
        }
      } catch (err) {
        this.errors = err
        console.log(err)
      }
    },

    async getError(reject) {
      try {
        let { data } = await this.axios.get('/import/errors/' + this.importId)
        this.importError = data
        return reject({
          title: 'Error!',
          body: this.importError,
          config: {
            closeOnClick: true
          }
        })
      } catch (err) {
        console.log(err)
      }
    },

    async getIncorrectErrors() {
      try {
        let { data } = await this.axios.get('/import/failure/' + this.importId)
        this.failures = data.errors
        this.unsuited = data.unsuited

        for (let item of Object.values(this.failures)) {
          for (let error of Object.values(item.errors)) {
            this.$snotify.info(error.column + error.message, 'Error in ' + item.row + ' row', {
              timeout: 30000,
              showProgressBar: true,
              closeOnClick: true,
              pauseOnHover: true
            });
          }
        }

        for (let item of Object.values(this.unsuited)) {
          for (let rules of Object.values(item.rules)) {
            this.$snotify.info(rules.column + rules.message, 'Missing in ' + item.row + ' row', {
              timeout: 30000,
              showProgressBar: true,
              closeOnClick: true,
              pauseOnHover: true,
              titleMaxLength: 30
            });
          }
        }

      } catch (err) {
          console.log(err)
      }
    }
  }
}
</script>

<style scoped>
.card {
  min-height: 400px
}

.upload {
  min-height: 600px
}
</style>