<template>
    <default-field
        :field="field"
        :errors="errors"
        :show-help-text="showHelpText"
    >
        <template slot="field">
            <div class="flex address-prefiller" ref="addressPrefiller">
                <div class="w-1/2">
                    <input
                        id="address_prefiller_zip_code_field"
                        name="address_prefiller_zip_code_field"
                        type="text"
                        class="w-full form-control form-input form-input-bordered"
                        v-bind:class="zipcodeError ? 'border-danger' : ''"
                        :placeholder="field.zipcode_placeholder"
                    />
                    <div
                        v-if="zipcodeError"
                        class="help-text error-text mt-2 text-danger"
                    >
                        {{ zipcodeErrorMessage }}
                    </div>
                </div>
                <div class="ml-2 w-1/2">
                    <input
                        id="address_prefiller_house_number_field"
                        name="address_prefiller_house_number_field"
                        type="text"
                        class="w-full form-control form-input form-input-bordered"
                        v-bind:class="houseNumberError ? 'border-danger' : ''"
                        :placeholder="field.house_number_placeholder"
                    />
                    <div
                        v-if="houseNumberError"
                        class="help-text error-text mt-2 text-danger"
                    >
                        {{ housenumberErrorMessage }}
                    </div>
                </div>
                <div>
                    <button
                        v-on:click="getDataFromApi($event)"
                        type="button"
                        class="btn btn-default ml-2 btn-primary inline-flex items-center relative"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            height="24"
                            viewBox="0 0 24 24"
                            width="24"
                        >
                            <path
                                fill="currentColor"
                                d="M17.01 14h-.8l-.27-.27c.98-1.14 1.57-2.61 1.57-4.23 0-3.59-2.91-6.5-6.5-6.5s-6.5 3-6.5 6.5H2l3.84 4 4.16-4H6.51C6.51 7 8.53 5 11.01 5s4.5 2.01 4.5 4.5c0 2.48-2.02 4.5-4.5 4.5-.65 0-1.26-.14-1.82-.38L7.71 15.1c.97.57 2.09.9 3.3.9 1.61 0 3.08-.59 4.22-1.57l.27.27v.79l5.01 4.99L22 19l-4.99-5z"
                            />
                        </svg>
                    </button>
                </div>
            </div>
            <div
                v-if="exceptionError"
                class="help-text error-text mt-2 text-danger"
            >
                {{ exceptionError }}
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
                zipcodeError: false,
                houseNumberError: false,
                exceptionError: null,
            };
        },

        /**
         * Mount the component.
         */
        mounted() {
            this.zipcodeErrorMessage = this.field.zipcode_error;
            this.housenumberErrorMessage = this.field.housenumber_error;
            this.keyup();

            this.$refs.addressPrefiller.querySelectorAll(
                '[name="address_prefiller_zip_code_field"]'
            )[0].value = this.prefillData(0);

            this.$refs.addressPrefiller.querySelectorAll(
                '[name="address_prefiller_house_number_field"]'
            )[0].value = this.prefillData(1);
        },

        methods: {
            keyup() {
                let vue = this;

                document.addEventListener("keyup", function(e) {
                    if (
                        e.target.getAttribute("id") ==
                        "address_prefiller_zip_code_field"
                    ) {
                        Nova.$emit(
                            vue.buildFieldName(vue.field.zipcode, e.target) +
                                "-value",
                            vue.getCurrentZipcodeValue(e.target)
                        );
                    } else if (
                        e.target.getAttribute("id") ==
                        "address_prefiller_house_number_field"
                    ) {
                        console.log("KeyUp");
                        Nova.$emit(
                            vue.buildFieldName(
                                vue.field.housenumber,
                                e.target
                            ) + "-value",
                            vue.getCurrentHousenumberValue(e.target)
                        );
                    }
                });
            },

            getCurrentZipcodeValue: function(event_target) {
                let element =
                    event_target.closest(".flexible-group-container") ??
                    event_target.closest(".card");

                let current_zipcode = element.querySelectorAll(
                    '[name="address_prefiller_zip_code_field"]'
                )[0].value;

                console.log(current_zipcode);

                return current_zipcode;
            },

            getCurrentHousenumberValue: function(event_target) {
                let element =
                    event_target.closest(".flexible-group-container") ??
                    event_target.closest(".card");

                return element.querySelectorAll(
                    '[name="address_prefiller_house_number_field"]'
                )[0].value;
            },

            shouldRunApiRequest: function(event_target) {
                this.zipcodeError = false;
                this.houseNumberError = false;
                this.exceptionError = null;

                let current_zipcode = this.getCurrentZipcodeValue(event_target);
                let current_housenumber = this.getCurrentHousenumberValue(
                    event_target
                );

                if (!current_zipcode) {
                    this.zipcodeError = true;
                    return false;
                }

                let zipcode = current_zipcode.replace(/\s+/g, "");
                if (zipcode.length != 6) {
                    this.zipcodeError = true;
                    return false;
                }

                if (!current_housenumber || current_housenumber.length == 0) {
                    this.houseNumberError = true;
                    return false;
                }

                return true;
            },

            resetResults: function() {},

            buildFieldName: function(field, event_target) {
                let flexible_id = this.getFlexibleId(event_target);
                if (flexible_id) {
                    return flexible_id + "__" + field;
                }
                return field;
            },

            getFlexibleId: function(event_target) {
                var flexible_id = null;

                let element = event_target.closest(".flexible-group-container");
                if (element) {
                    flexible_id = element.id;
                }
                return flexible_id;
            },

            getDataFromApi: function(event) {
                let vue = this;

                let flexible_id = this.getFlexibleId(event.target);

                if (!this.shouldRunApiRequest(event.target)) {
                    this.resetResults();
                    return;
                }

                let current_zipcode = this.getCurrentZipcodeValue(event.target);
                let current_housenumber = this.getCurrentHousenumberValue(
                    event.target
                );

                let formData = new FormData();
                formData.append("zipcode", current_zipcode);
                formData.append("housenumber", current_housenumber);
                formData.append("street", this.field.street);
                formData.append("city", this.field.city);
                formData.append("province", this.field.province);
                formData.append("country", this.field.country);
                formData.append("latitude", this.field.latitude);
                formData.append("longitude", this.field.longitude);
                formData.append("flexible_id", flexible_id);

                Nova.request()
                    .post("/zipcode/get-address-information", formData)
                    .then((response) => {
                        if (response.data.error) {
                            vue.exceptionError = response.data.error;
                        } else {
                            for (const property in response.data) {
                                Nova.$emit(
                                    property + "-value",
                                    response.data[property]
                                );
                            }
                        }
                    })
                    .catch(function(error) {
                        console.log(error);
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
            },
        },
    };
</script>
