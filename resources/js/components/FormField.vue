<template>
    <default-field
        :field="field"
        :errors="errors"
        :show-help-text="showHelpText"
    >
        <template slot="field">
            <div class="flex">
                <div class="w-1/2">
                    <input
                        :id="field.name"
                        type="text"
                        class="w-full form-control form-input form-input-bordered"
                        :class="errorClasses"
                        :placeholder="field.name"
                        v-model="zipcode"
                    />
                </div>
                <div class="ml-2 w-1/2">
                    <input
                        :id="field.name_2"
                        type="text"
                        class="w-full form-control form-input form-input-bordered"
                        :class="errorClasses"
                        :placeholder="field.name_2"
                        v-model="housenumber"
                    />
                </div>
            </div>
        </template>
    </default-field>
</template>

<script>
import { FormField, HandlesValidationErrors } from "laravel-nova";

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ["resourceName", "resourceId", "field"],

    data() {
        return {
            zipcode: this.prefillData(0),
            housenumber: this.prefillData(1)
        };
    },

    /**
     * Mount the component.
     */
    mounted() {
        // this.setInitialValue();
        let vue = this;

        document.addEventListener("keyup", function(e) {
            if (vue.field.name == e.target.getAttribute("id")) {
                vue.field.value[0] = vue.zipcode;
                vue.getDataFromApi();
            } else if (vue.field.name_2 == e.target.getAttribute("id")) {
                vue.field.value[1] = vue.housenumber;
                vue.getDataFromApi();
            }
        });
    },

    methods: {
        shouldRunApiRequest: function() {
            let zipcode = this.zipcode.replace(/\s+/g, "");
            if (zipcode.length != 6) {
                return false;
            }

            if (this.housenumber.length == 0) {
                return false;
            }

            return true;
        },

        getDataFromApi: function() {
            if (!this.shouldRunApiRequest()) {
                return;
            }

            let formData = new FormData();
            formData.append("zipcode", this.zipcode);
            formData.append("housenumber", this.housenumber);
            formData.append("street", this.field.street);
            formData.append("city", this.field.city);
            formData.append("province", this.field.province);
            formData.append("country", this.field.country);
            formData.append("latitude", this.field.latitude);
            formData.append("longitude", this.field.longitude);

            Nova.request()
                .post("/zipcode/get-address-information", formData)
                .then(response => {
                    console.log(response.data);
                    for (const property in response.data) {
                        Nova.$emit(
                            property + "-value",
                            response.data[property]
                        );
                    }
                });
        },

        setInitialValue: function setInitialValue() {
            this.value = !(
                this.field.value === undefined || this.field.value === null
            )
                ? this.field.value
                : ["", ""];
        },

        /*
         * Set the initial, internal value for the field.
         */
        prefillData(key) {
            var computed_value = this.field.value || [];
            if (key in computed_value) {
                return computed_value[key];
            }
            return "";
        }
    }
};
</script>
