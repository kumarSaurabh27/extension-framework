// var ChannelListView = Backbone.View.extend({
//     el: $('#configure>.uv-app-screen'),
//     appSplashTemplate: _.template($('#app-splash-template').html()),
//     channelListTemplate: _.template($('#channel-list-template').html()),
//     channelListSelector: '#channel-list',
//     events: {
//         'click .add-app-channel': 'addChannelView',
//     },
//     render: function(channelCollection) {
//         if (channelCollection.length) {
//             this.$el.html(this.channelListTemplate());
//             _.each(channelCollection.models, function (item) {
//                 this.renderChannel(item);
//             }, this);
//         }  else {
//             this.$el.html(this.appSplashTemplate());
//         }
//     },
//     renderChannel: function(item) {
//         var channelView = new ChannelView({
//             model: item
//         });

//         $(this.channelListSelector).append(channelView.render().el);
//     },
//     addChannelView: function(e) {
//         channelForm.model.clear();
//         channelForm.render();
//         $('.uv-aside-back').addClass('edit-back');
//     },
// });

// var ChannelFormView = Backbone.View.extend({
//     el: $('#configure>.uv-app-screen'),
//     ChannelFormTemplate: _.template($('#channel-form-template').html()),
//     events: {
//         'blur input': 'formChanged',
//         'change select': 'formChanged',
//         'click #save-channel': 'submitForm',
//     },
//     render: function(value) {
//         Backbone.Validation.bind(this);
//         $('.uv-app-add-channel').remove();
//         $('.uv-app-splash').hide();
//         $('.uv-app-list-channels').hide();
//         var currentTemplate, modelJson;
//         $(this.el).append(currentTemplate = this.ChannelFormTemplate(modelJson = this.model.toJSON()));
//         this.activateTabs();
//         if (typeof(this.AddChecked) == 'function') {
//             this.AddChecked();
//         }
//     },
//     activateTabs: function() {
//         $('.uv-box-tab ul li a').on('click', function(e){
//             e.preventDefault();
//             $('.uv-box-tab ul li a').removeClass('uv-box-tab-active');
//             $(this).addClass('uv-box-tab-active')

//             $('.uv-tab-view .uv-tab-view').removeClass('uv-tab-view-active');
//             $('#'+$(this).attr('data-href')).addClass('uv-tab-view-active')
//         });
//     },
//     formChanged: function(e) {
//         this.model.set(Backbone.$(e.currentTarget).attr('name'), Backbone.$(e.currentTarget).val())
//         this.model.isValid([Backbone.$(e.currentTarget).attr('name')])
//     },
//     submitForm: function(e) {
//         e.preventDefault();

//         form = $(e.target).closest('form');
//         this.model.set(form.serializeObject());
//         if (this.model.isValid(true)) {
//             $(e.target).attr('disabled', 'disabled');
//             form.attr("action", url);
//             form.submit();
//         }
//     },
// });

$(function () {
    const templates = {
        loading_screen_template: $("#shopify-dashboard-loading-screen-template").html().replace(/&lt;/g, '<').replace(/&gt;/g, '>'),
        welcome_screen_template: $("#shopify-dashboard-welcome-screen-template").html().replace(/&lt;/g, '<').replace(/&gt;/g, '>'),
        manage_stores_template: $("#shopify-dashboard-manage-stores-template").html().replace(/&lt;/g, '<').replace(/&gt;/g, '>'),
    };

    var Animation = Backbone.View.extend({
        el: $("#applicationDashboard"),
        loading_screen_template: _.template(templates.loading_screen_template),
        enable: function (text) {
            this.$el.append(this.loading_screen_template({ text: text }));
        },
        disable: function () {
            this.$el.find('.shopify-dashboard-loader').remove();
        }
    });

    var StoreConfigurationModel = Backbone.Model.extend({
        idAttribute: "id",
        validation: {}
    });

    var StoreConfigurationView = Backbone.View.extend({
        el: $("#applicationDashboard"),
        events: {
            'click .uv-app-shopify-cta-setup' : 'setupApplication',
            'click .edit-settings' : 'editSettings',
            'click .delete-channel' : 'confirmRemove',
        },
        initialize: function() {
            console.log('init shopify view');

            $.ajax({
                url: "./../../api/akshay/shopify/ecommerce-connector",
                success: function (response) {
                    console.log('success:', response);
                },
                error: function (model, xhr, options) {
                    console.log('error:', model, xhr, options);
                }
            });
        },
        render: function() {
            this.$el.html(this.template(this.model.toJSON()));
            return this;
        },
        setupApplication: function (e) {
            console.log(e);
        }
    });
    
    var ShopifyConfiguration = Backbone.Collection.extend({
        url: "./../../api/akshay/shopify/ecommerce-connector",
        model: StoreConfigurationModel,
        parse: function (response) {
            return response.stores;
        },
        syncData: function(dashboard) {
            dashboard.animations.enable('Please wait while your dashboard is being prepared...');

            this.fetch({
                reset: true,
                data: {
                    endpoint: 'store-configurations'
                },
                success: function(collection, response) {
                    dashboard.animations.disable();

                    // if (collection.length > 0) {
                    //     dashboard.renderSettingsPanel();
                    // } else {
                    //     dashboard.renderWelcomeScreen();
                    // }
                },
                error: function (response) {
                    console.log('error:', response)
                }
            });
        }
    });

    var WelcomeScreen = Backbone.View.extend({
        el: $("#applicationDashboard"),
        template: _.template(templates.welcome_screen_template)
    });

    var DashboardPanel = Backbone.View.extend({
        el: $("#applicationDashboard"),
        template: _.template(templates.manage_stores_template),
        initialize: function() {
        },
        render: function () {
            this.$el.html(this.manage_stores_template({ stores: this.configurations.models}));
        }
    });

    var Shopify = Backbone.View.extend({
        el: $("#applicationDashboard"),
        initialize: function() {
            this.$el.empty();
            this.animations = new Animation();
            this.configurations = new ShopifyConfiguration();

            this.configurations.syncData(this);
        }
    });

    let dashboard = new Shopify();
});