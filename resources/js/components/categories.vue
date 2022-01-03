<template>
    <div class="categoriesComponent container-fluid">
        <div v-if="isCategoriesEmpty">
            <h2>Nie dodano żadnych kategorii, kliknik przycisk <strong>Dodaj kategorię</strong> by rozpocząć</h2>
        </div>
        <div v-else>
            <h4>Przeglądaj kategorie</h4>
            <table class="table table-hover dataTable no-footer">
                <thead>
                <tr>
                    <th class="dt-not-orderable sorting_disabled">
                        Nazwa
                    </th>
                    <th class="dt-not-orderable sorting_disabled">
                        Ilość parametrów
                    </th>
                    <th>Opcje</th>
                </tr>
                </thead>
                <tbody>
                <template v-for="(item, index) in this.categoryData">
                    <tr >
                        <td v-html="item.name" style="vertical-align: middle">
                        </td>
                        <td style="vertical-align: middle" v-html="getParamsCount(index)"></td>
                        <td>
                            <button class="btn btn-sm btn-danger">Usuń kategorię i parametry</button>
                            <button class="btn btn-sm btn-primary" v-on:click="editParameters(item.id)">Edytuj parametry</button>
                        </td>
                    </tr>
                    <tr :id="'categoryParams'+item.id" style="display: none" class="editParametersDiv">
                        <td colspan="3">
                            <h5>Parametry</h5>
                            <div v-if="item.parameters.length == 0">
                                Nie dodano parametrów, kliknij poniższy przycisk aby dodać parametr
                            </div>
                            <div v-else>
                                tutaj parametry
                            </div>
                            <div class="" :id="'parametersAdd'+item.id"  v-if="displayParametersForm == item.id">
<!--                                formularz dodania parametru-->
                                <div class="panel panel-bordered">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="form-group  col-md-12 ">
                                                <label class="control-label">
                                                    Parametr
                                                </label>
                                                <input type="text" class="form-control" v-model="newParameterName" placeholder="Podaj nazwę nowego parametru" />
                                            </div>
                                            <div class="form-group  col-md-12 ">
                                                <label class="control-label">
                                                    Punktacja minimum
                                                </label>
                                                <select v-model="newParameterRatingMin" class="form-control">
                                                    <option value="0">0</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                    <option value="6">6</option>
                                                    <option value="7">7</option>
                                                    <option value="8">8</option>
                                                    <option value="9">9</option>
                                                    <option value="10">10</option>
                                                    <option value="11">11</option>
                                                    <option value="12">12</option>
                                                    <option value="13">13</option>
                                                    <option value="14">14</option>
                                                    <option value="15">15</option>
                                                    <option value="16">16</option>
                                                    <option value="17">17</option>
                                                    <option value="18">18</option>
                                                    <option value="19">19</option>
                                                    <option value="20">20</option>
                                                </select>
                                            </div>
                                            <div class="form-group  col-md-12 ">
                                                <label class="control-label">
                                                    Punktacja maksumim
                                                </label>
                                                <select v-model="newParameterRatingMax" class="form-control">
                                                    <option value="0">0</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                    <option value="6">6</option>
                                                    <option value="7">7</option>
                                                    <option value="8">8</option>
                                                    <option value="9">9</option>
                                                    <option value="10">10</option>
                                                    <option value="11">11</option>
                                                    <option value="12">12</option>
                                                    <option value="13">13</option>
                                                    <option value="14">14</option>
                                                    <option value="15">15</option>
                                                    <option value="16">16</option>
                                                    <option value="17">17</option>
                                                    <option value="18">18</option>
                                                    <option value="19">19</option>
                                                    <option value="20">20</option>
                                                </select>
                                            </div>
                                            <div class="form-group  col-md-12 ">
                                                <label class="control-label">
                                                    Czy parametr widoczny dla laboratorium
                                                </label>
                                                <select v-model="newParameterLabVisible" class="form-control">
                                                    <option value="0">nie</option>
                                                    <option value="1">tak</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-footer">
                                        <button class="btn btn-primary save " v-on:click="addParameterSave(item.id, index)">Dodaj nowy parametr</button>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-primary" v-on:click="addParameter(item.id)">Dodaj nowy parametr</button>
                            </div>
                        </td>
                    </tr>
                </template>
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12">
                <button class="btn btn-sm btn-primary " v-on:click="addCategory()">Dodaj kategorię</button>
            </div>
        </div>

        <div id="addCategory" style="display: none">
            <div class="panel panel-bordered">
                <div class="panel-body">
                    <div class="row">
                        <div class="form-group  col-md-12 ">
                            <label class="control-label">
                                Podaj nazwę kategorii
                            </label>
                            <input type="text" class="form-control" v-model="newCategoryName" placeholder="Podaj nazwę nowej kategorii" />
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <button class="btn btn-primary save " v-on:click="addCategorySave()">Dodaj nową kategorię</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    mounted() {
        // console.log(this.categories);
        this.categoryData = this.categories;
    },
    props: [
        'categories'
        ],
    data: function () {
        return {
            categoryData: [],
            newCategoryName: "",
            displayParametersForm: -1,

            newParameterName: "",
            newParameterRatingMin: 0,
            newParameterRatingMax: 10,
            newParameterLabVisible: 0,
        }
    },
    methods: {
        addCategory: function() {
            this.newCategoryName = '';
            $('#addCategory').slideDown();
        },
        addCategorySave: function() {
            var self = this;
            if (this.newCategoryName == "") {
                alert('Proszę podać nazwę kategorii');
            } else {
                console.log(this.categoryData);

                // sprawdzę czy wpis już mam
                try {
                    if (!Array.isArray(this.categoryData) || self.categoryData.length == 0) {
                        let item = {
                            "name": self.newCategoryName,
                            "parameters": {}
                        }
                        this.categoryData.push(item);
                    } else {
                        // check;
                    }
                } catch (e) {
                    console.log(e);
                }
                $('#addCategory').slideUp();
            }
        },
        editParameters: function(index) {
            console.log(index);
            $('.editParametersDiv').hide();
            let id = '#categoryParams'+index;
            $(id).show();
        },
        addParameter: function(id)
        {
            this.displayParametersForm = id;
            this.newParameterRatingMin = 0;
            this.newParameterRatingMax = 20;
            this.newParameterName = "";
            this.newParameterLabVisible = 0;
        },
        getParamsCount: function (index) {
            try {
                return this.categoryData[index]["parameters"].length;
            } catch (e) {

            }
            return 0;
        },
        addParameterSave: function (id, index) {
            
        }
    },
    computed: {
        isCategoriesEmpty: function() {
            let self = this;
            console.log(self.categories);
            if (typeof self.categories == 'object' && self.categories.length == 0) {
                return true;
            }
            if ((Array.isArray(self.categories) && self.categories.length == 0)) {
                return true;
            }
            return false;
        }
    }
}
</script>
