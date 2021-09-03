Nova.booting((Vue, router, store) => {
    Vue.config.devtools = true;
    Vue.component("index-zipcode", require("./components/IndexField").default);
    Vue.component(
        "detail-zipcode",
        require("./components/DetailField").default
    );
    Vue.component("form-zipcode", require("./components/FormField").default);
});
