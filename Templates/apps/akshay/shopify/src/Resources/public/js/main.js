// Load the application once the DOM is ready
$(function () {
    // Initialize templates
    const templates = {
        loading_screen_template: $("#akshay-shopify-loading-screen-template").html().replace(/&lt;/g, '<').replace(/&gt;/g, '>'),
        welcome_section_template: $("#akshay-shopify-welcome-section-template").html().replace(/&lt;/g, '<').replace(/&gt;/g, '>'),
        manage_stores_template: $("#akshay-shopify-manage-stores-template").html().replace(/&lt;/g, '<').replace(/&gt;/g, '>'),
        manage_store_form_template: $("#akshay-shopify-manage-store-form-template").html().replace(/&lt;/g, '<').replace(/&gt;/g, '>'),
    };

    // Animated Loaders
    var DashboardAnimations = Backbone.View.extend({
        el: $("#applicationDashboard"),
        template: _.template(templates.loading_screen_template),
        showDashboardLoader: function (text) {
            this.$el.append(this.template({ text: text }));
        },
        hideDashboardLoader: function () {
            this.$el.find('.shopify-dashboard-loader').remove();
        }
    });

    var ShopifyStore = Backbone.Model.extend({
        url: "./ecommerce-connector/api?endpoint=save-store-configuration",
        defaults: function() {
            return {
                domain: "",
                api_key: "",
                api_password: "",
                enabled: false,
            };
        },
        validate: function(attributes, options) {
            let validationErrors = {};

            for (let name in attributes) {
                let result = this.validateAttribute(name, attributes[name]);

                if (result !== true) {
                    validationErrors[name] = result;
                }
            }

            if (false == $.isEmptyObject(validationErrors)) {
                return validationErrors;
            }
        },
        validateAttribute: function(name, value) {
            switch (name) {
                case 'domain':
                case 'api_key':
                case 'api_password':
                    if (value == undefined || value == '') return 'This field cannot be left empty.';
                    break;
                default:
                    break;
            }

            return true;
        }
    });

    var ShopifyStoreCollection = Backbone.Collection.extend({
        url: "./ecommerce-connector/api?endpoint=get-configurations",
        model: ShopifyStore,
        parse: function (response) {
            return response.stores;
        },
        fetch: function () {
            let collection = this;

            $.ajax({
                type: 'GET',
                url: this.url,
                dataType: 'json',
                success: function(response) {
                    collection.reset(collection.parse(response));
                },
                error: function (response) {
                    console.log('error:', response)
                }
            });
        }
    });

    var ShopifyStoreSettingsForm = Backbone.View.extend({
        el: $("#applicationDashboard"),
        template: _.template(templates.manage_store_form_template),
        events: {
            'input form input': 'setAttribute',
            'submit form': 'submitForm'
        },
        render: function(el) {
            console.log('render form:', this.model.toJSON());
            this.listenTo(this.model, 'sync', this.handleSync);
            this.listenTo(this.model, 'error', this.handleSyncFailure);

            el.html(this.template(this.model.toJSON()));
        },
        setAttribute: function(ev) {
            let name = $(ev.currentTarget)[0].name.trim();
            let value = $(ev.currentTarget)[0].value.trim();

            if (this.model.has(name)) {
                this.model.set(name, value);
            }
        },
        submitForm: function (ev) {
            ev.preventDefault();

            if (this.model.isValid()) {
                console.log('saving model');
                this.model.save();
            }
        },
        handleSync: function (model, response, options) {
            console.log('model synced:', model);
            shopifyStoreCollection.add(model);
        },
        handleSyncFailure: function (model, xhr, options) {
            console.log('failed to sync model:', model, xhr, options);
        }
    });

    var Welcome = Backbone.View.extend({
        el: $("#applicationDashboard"),
        template: _.template(templates.welcome_section_template),
        events: {
            'click .uv-app-shopify-cta-setup': 'setupStore'
        },
        render: function () {
            this.$el.html(this.template());
        },
        setupStore: function(e) {
            app.animation.showDashboardLoader();

            let self = this;
            this.model = new ShopifyStore();
            this.welcomeForm = new ShopifyStoreSettingsForm({ model: this.model });
            
            this.$el.find('.welcome-screen.banner').fadeOut(100, () => {
                self.welcomeForm.render(this.$el.find('.welcome-screen.configure-store form'));
                self.$el.find('.welcome-screen.configure-store').fadeIn(200, () => {
                    app.animation.hideDashboardLoader();
                });
            });
        }
    });

    var Dashboard = Backbone.View.extend({
        el: $("#applicationDashboard"),
        template: _.template(templates.manage_stores_template),
        initialize: function() {
        },
        render: function () {
            console.log('render application dashboard');
            // this.$el.html(this.manage_stores_template({ stores: this.configurations.models}));
        }
    });

    var ShopifyApp = Backbone.View.extend({
        el: $("#applicationDashboard"),
        initialize: function(shopifyStoreCollection) {
            this.$el.empty();
            this.animation = new DashboardAnimations();

            this.listenTo(shopifyStoreCollection, 'add', this.addShopifyStore);
            this.listenTo(shopifyStoreCollection, 'reset', this.reset);
            this.listenTo(shopifyStoreCollection, 'all', this.render);

            this.animation.showDashboardLoader('Please wait while your dashboard is being prepared...');
            shopifyStoreCollection.fetch();
        },
        render: function() {
            console.log('render:', shopifyStoreCollection);
            this.animation.hideDashboardLoader();

            if (!shopifyStoreCollection.length) {
                if (false == this.hasOwnProperty('welcome') || typeof this.welcome == 'undefined') {
                    this.welcome = new Welcome();
                }

                this.welcome.render();
            } else {
                if (false == this.hasOwnProperty('dashboard') || typeof this.dashboard == 'undefined') {
                    this.dashboard = new Dashboard();
                }

                this.dashboard.render();
            }
        },
        addShopifyStore: function(todo) {
            console.log('addShopifyStore:', shopifyStoreCollection);
            // var view = new TodoView({model: todo});
            // this.$("#todo-list").append(view.render().el);
        },
        reset: function() {
            console.log('reset:', shopifyStoreCollection);
            shopifyStoreCollection.each(this.addShopifyStore, this);
        },
    });

    let shopifyStoreCollection = new ShopifyStoreCollection();
    let app = new ShopifyApp(shopifyStoreCollection);
});