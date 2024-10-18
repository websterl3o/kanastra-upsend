<template>
    <div class="rounded-lg bg-white p-6 w-1/2">
        <form action="#" method="post">
            <div class="space-y-5">
                <div class="border-b border-slate-900/10 pb-5">
                    <div class="col-span-full">
                        <label for="cover-photo" class="block text-2xl font-medium leading-6 text-slate-900 pb-2">Lista de cobrança</label>
                        <div v-if="errorMessage" class="flex bg-red-500 rounded-lg">
                            <div class="flex">
                                <svg class="h-6 w-6 text-white m-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm leading-5 text-white m-2">{{ errorMessage }}</p>
                            </div>
                        </div>
                        <div v-if="successMessage" class="flex bg-emerald-500 rounded-lg">
                            <div class="flex">
                                <svg class="h-6 w-6 text-white m-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" stroke="white" fill="none"></circle>
                                    <path d="M9 12l2 2 4-4" stroke="white"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm leading-5 text-white m-2">{{ successMessage }}</p>
                            </div>
                        </div>
                        <div class="mt-2 flex justify-center rounded-lg border border-dashed border-slate-900/25 px-6 py-10" @click="triggerFileUpload">
                            <div id="select-file" class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-300" version="1.1" id="Layer_1" viewBox="0 0 309.267 309.267" xml:space="preserve">
                                    <g>
                                        <path style="fill:#3DB39E;" d="M38.658,0h164.23l87.049,86.711v203.227c0,10.679-8.659,19.329-19.329,19.329H38.658 c-10.67,0-19.329-8.65-19.329-19.329V19.329C19.329,8.65,27.989,0,38.658,0z" />
                                        <path style="fill:#2F8A78;" d="M289.658,86.981h-67.372c-10.67,0-19.329-8.659-19.329-19.329V0.193L289.658,86.981z" />
                                        <path style="fill:#8BD1C5;" d="M77.317,125.64v125.64H231.95V125.64H77.317z M193.292,135.304v19.329h-19.329v-19.329H193.292z M135.304,183.627h-19.329v-19.32h19.329V183.627z M144.969,164.308h19.329v19.32h-19.329V164.308z M135.304,193.302v19.32h-19.329 v-19.32H135.304z M144.969,193.302h19.329v19.32h-19.329V193.302z M173.963,193.302h19.329v19.32h-19.329V193.302z M173.963,183.627v-19.32h19.329v19.32L173.963,183.627L173.963,183.627z M164.298,135.304v19.329h-19.329v-19.329H164.298z M135.304,135.304v19.329h-19.329v-19.329H135.304z M86.981,135.304h19.329v19.329H86.981V135.304z M86.981,164.308h19.329v19.32 H86.981V164.308z M86.981,193.302h19.329v19.32H86.981V193.302z M86.981,241.625v-19.339h19.329v19.339H86.981z M115.975,241.625 v-19.339h19.329v19.339H115.975z M144.969,241.625v-19.339h19.329v19.339H144.969z M173.963,241.625v-19.339h19.329v19.339H173.963 z M222.286,241.625h-19.329v-19.339h19.329V241.625z M222.286,212.621h-19.329v-19.32h19.329V212.621z M222.286,183.627h-19.329 v-19.32h19.329V183.627z M222.286,154.634h-19.329v-19.329h19.329V154.634z" />
                                    </g>
                                </svg>
                                <div v-if="!fileUpload">
                                    <div class="mt-4 flex text-sm leading-6 text-gray-600">
                                        <label for="file" class="relative cursor-pointer rounded-md bg-white font-semibold text-emerald-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-emerald-600 focus-within:ring-offset-2 hover:text-emerald-500">
                                            <span>Selecione seu arquivo aqui!</span>
                                            <input id="file-upload" name="file" type="file" class="sr-only" @change="handleFileUpload" required ref="fileUpload">
                                        </label>
                                    </div>
                                    <p class="text-xs leading-5 text-gray-600">CSV de até 200MB</p>
                                </div>
                                <div v-else>
                                    <p class="mt-4 text-sm leading-6 text-gray-600">{{ fileUpload.name }}</p>
                                    <span class="rounded-lg bg-red-500 bor text-xs leading-5 text-white font-medium px-3 py-1">Clique para limpar.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button @click="sendForm" id="sendButton" class="flex w-full mt-3 justify-center rounded-md bg-emerald-600 p-3 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">Enviar</button>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    props: {
        uploadUrl: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            fileUpload: null,
            errorMessage: null,
            successMessage: null,
        };
    },
    methods: {
        triggerFileUpload() {
            if (this.fileUpload) {
                this.fileUpload = null;
                return;
            }
            this.$refs.fileUpload.click();
        },
        handleFileUpload(event) {
            const file = event.target.files[0];
            const validFormats = ['text/csv'];
            const maxSize = 200 * 1024 * 1024;

            if (!validFormats.includes(file.type)) {
                this.errorMessage = 'Formato de arquivo inválido. Por favor, envie um arquivo CSV.';
                this.fileUpload = null;
                return;
            }

            if (file.size > maxSize) {
                this.errorMessage = 'O arquivo é muito grande. O tamanho máximo permitido é 200MB.';
                this.fileUpload = null;
                return;
            }

            this.fileUpload = file;
            this.errorMessage = null;
        },
        sendForm: function() {
            document.getElementById('sendButton').disabled = true;
            const formData = new FormData();
            formData.append('csrf', document.querySelector('meta[name="csrf-token"]').getAttribute('content'),);
            formData.append('file', this.fileUpload);

            axios.post(this.uploadUrl, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                document.getElementById('sendButton').disabled = false;
                debugger
                this.successMessage = response.data.message;
                this.fileUpload = null;
            }).catch(error => {
                document.getElementById('sendButton').disabled = false;
            });
        }
    }
};
</script>
