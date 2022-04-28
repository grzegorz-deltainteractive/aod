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
                    <th>Czy warunek bezwględny?</th>
                    <th>Opcje</th>
                </tr>
                </thead>
                <tbody>
                <template v-for="(item, index) in this.categoryData">
                    <tr >
                        <td v-html="item.name" style="vertical-align: middle">
                        </td>
                        <td style="vertical-align: middle" v-html="getParamsCount(index)"></td>
                        <td style="vertical-align: middle">
                            <span v-if="item.is_requested == 1">Tak</span>
                            <span v-else>Nie</span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-danger" v-on:click="deleteCategory(index)">Usuń kategorię i parametry</button>
                            <button class="btn btn-sm btn-primary" v-on:click="editParameters(item.id, index)">Edytuj parametry</button>
                        </td>
                    </tr>
                    <tr :id="'categoryParams'+index" style="display: none" class="editParametersDiv">
                        <td colspan="4">
                            <h5>Parametry</h5>
                            <div v-if="item.parameters.length == 0">
                                <p>Nie dodano parametrów, kliknij poniższy przycisk aby dodać parametr.</p>
                            </div>
                            <div v-else>
                                <table class="table table-hover dataTable no-footer">
                                    <thead>
                                    <tr>
                                        <th>Nazwa</th>
                                        <th>Ocena minimalna</th>
                                        <th>Ocena maksymalna</th>
                                        <th v-show="item.is_requested == 0">Czy widzi to laboratorium</th>
                                        <th>Opcje</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(item2, index2) in item.parameters">
                                        <td style="vertical-align: middle" v-html="item2.name"></td>
                                        <td style="vertical-align: middle" v-html="item2.rating_min"></td>
                                        <td style="vertical-align: middle" v-html="item2.rating_max"></td>
                                        <td style="vertical-align: middle"  v-show="item.is_requested == 0">
                                            <span v-if="item2.visible_for_lab == 0">nie</span>
                                            <span v-else>tak</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" @click="editParameter(index, index2)">Edytuj parametr</button>
                                            <button class="btn btn-sm btn-danger" @click="deleteParameter(index, index2)">Usuń parametr</button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
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
                                                <input type="text" class="form-control" v-model="newParameterName" placeholder="Podaj nazwę nowego parametru" id="newParameterName" />
                                            </div>
                                            <div class="form-group  col-md-12 ">
                                                <label class="control-label">
                                                    Punktacja minimum
                                                </label>
                                                <select v-model="newParameterRatingMin" class="form-control" v-if="newParamaterIsRequested == 0">
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
                                                <select v-model="newParameterRatingMin" class="form-control" v-else>
                                                    <option value="0">0</option>

                                                </select>
                                            </div>
                                            <div class="form-group  col-md-12 ">
                                                <label class="control-label">
                                                    Punktacja maksumim
                                                </label>
                                                <select v-model="newParameterRatingMax" class="form-control" v-if="newParamaterIsRequested == 0">
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
                                                <select v-model="newParameterRatingMax" class="form-control" v-else>
                                                    <option value="1">1</option>

                                                </select>
                                            </div>
                                            <div class="form-group  col-md-12 " v-show="newParamaterIsRequested == 0">
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
                                        <button class="btn btn-primary save " v-on:click="addParameterSave(item.id, index)">Zapisz parametr</button>
                                        <button class="btn btn-secondary" v-on:click="cancelParameterSave(item.id, index)">Anuluj dodawanie parametru</button>
                                    </div>
                                </div>
                            </div>
                            <div class="" :id="'parametersEdit'+editParameterId"  v-if="editParameterId != -1">
                                <!--                                formularz dodania parametru-->
                                <div class="panel panel-bordered">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="form-group  col-md-12 ">
                                                <label class="control-label">
                                                    Parametr
                                                </label>
                                                <input type="text" class="form-control" v-model="newParameterName" placeholder="Podaj nazwę nowego parametru" id="newParameterName" />
                                            </div>
                                            <div class="form-group  col-md-12 ">
                                                <label class="control-label">
                                                    Punktacja minimum
                                                </label>
                                                <select v-model="newParameterRatingMin" class="form-control" v-if="newParamaterIsRequested == 0">
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
                                                <select v-model="newParameterRatingMin" class="form-control" v-else>
                                                    <option value="0">0</option>

                                                </select>
                                            </div>
                                            <div class="form-group  col-md-12 ">
                                                <label class="control-label">
                                                    Punktacja maksumim
                                                </label>
                                                <select v-model="newParameterRatingMax" class="form-control" v-if="newParamaterIsRequested == 0">
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
                                                <select v-model="newParameterRatingMax" class="form-control" v-else>
                                                    <option value="1">1</option>

                                                </select>
                                            </div>
                                            <div class="form-group  col-md-12 " v-show="newParamaterIsRequested == 0">
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
                                        <button class="btn btn-primary save " v-on:click="editParameterSave(item.id, index)">Zaktualizuj parametr</button>
                                        <button class="btn btn-secondary" v-on:click="editParameterCancel()">Anuluj edycję parametru</button>
                                    </div>
                                </div>
                            </div>
                            <div v-if="displayParametersForm == -1">
                                <button class="btn btn-sm btn-primary" v-on:click="addParameter(item.id, item.is_requested)">Dodaj nowy parametr</button>
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
                            <input type="text" class="form-control" v-model="newCategoryName" placeholder="Podaj nazwę nowej kategorii" id="newCategoryName" />
                        </div>
                        <div class="form-group col-md-12">
                            <label class="control-label" for="newCategoryRequest">
                                Czy to kategoria warunków bezwględnych dla dostawcy?
                            </label>
                            <input type="checkbox" class="form-controll" v-model="newCategoryRequest" id="newCategoryRequest" />
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <button class="btn btn-primary save " v-on:click="addCategorySave()">Dodaj nową kategorię</button>
                </div>
            </div>
        </div>
        <a href="/admin/pools/" class="btn btn-sm" style="background-color: #cccccc">Zapisz - powrót do definicji</a>
    </div>
</template>

<script>
export default {
    mounted() {
        // console.log(this.categories);
        this.categoryData = this.categories;
    },
    props: [
        'categories', 'saveUrl'
        ],
    data: function () {
        return {
            categoryData: [],
            newCategoryName: "",
            newCategoryRequest: 0,
            displayParametersForm: -1,
            newParamaterIsRequested: 0,

            newParameterName: "",
            newParameterRatingMin: 0,
            newParameterRatingMax: 10,
            newParameterLabVisible: 0,

            prevParametrsIndexShow: -1,

            editParameterId: -1,
            tmpIndex2: 0

        }
    },
    methods: {
        addCategory: function() {
            this.newCategoryName = '';
            $('#addCategory').slideDown();
            $('#newCategoryName').focus();
        },
        addCategorySave: function() {
            var self = this;
            if (this.newCategoryName == "") {
                alert('Proszę podać nazwę kategorii');
            } else {
                // sprawdzę czy wpis już mam
                try {
                    if (typeof self.categoryData == 'object' && self.categoryData.length == 0) {
                        try {
                            let item = {
                                "name": self.newCategoryName,
                                "is_requested": self.newCategoryRequest,
                                "parameters": []
                            }
                            this.categoryData.push(item);
                            this.saveForm();
                        } catch (e2) {
                            console.log(e2);
                        }
                    } else if (Array.isArray(this.categoryData) && this.categoryData.length == 0) {
                        // console.log('2');
                        let item = {
                            "name": self.newCategoryName,
                            "is_requested": self.newCategoryRequest,
                            "parameters": []
                        }
                        this.categoryData.push(item);
                        this.saveForm();
                    } else {
                        if (typeof this.categoryData == 'object') {

                            let max = Object.keys(this.categoryData).length;
                            let exists = false;
                            for (let i=0; i<max; i++) {
                                if (this.categoryData[i].name == self.newCategoryName) {
                                    exists = true;
                                }
                            }
                            if (exists) {
                                alert('Kategoria o tej samej nazwie już istnieje!');
                            } else {
                                let item = {
                                    "name": self.newCategoryName,
                                    "is_requested": self.newCategoryRequest,
                                    "parameters": []
                                }
                                this.categoryData.push(item);
                                this.saveForm();
                            }
                        } else {
                            console.log('nie');
                        }
                    }
                } catch (e) {
                    console.log(e);
                    // let item = {
                    //     "name": self.newCategoryName,
                    //     "parameters": {}
                    // }
                    // this.categoryData.push(item);
                }
                $('#addCategory').slideUp();
            }
        },
        editParameters: function(newId, index) {
            $('.editParametersDiv').hide();
            let id = '#categoryParams'+index;
            if (this.prevParametrsIndexShow != index) {
                this.prevParametrsIndexShow = index;
                $(id).show();
            } else {
                this.prevParametrsIndexShow = -1;
                $(id).hide();
            }
        },
        editParameter: function(index, index2)
        {
            this.editParameterId = index;
            this.newParameterRatingMin = this.categoryData[index]['parameters'][index2]['rating_min'];
            this.newParameterRatingMax = this.categoryData[index]['parameters'][index2]['rating_max'];
            this.newParameterLabVisible = this.categoryData[index]['parameters'][index2]['visible_for_lab'];
            this.newParameterName = this.categoryData[index]['parameters'][index2]['name'];
            this.tmpIndex2 = index2;
        },
        editParameterCancel: function()
        {
            this.editParameterId = -1;
        },
        addParameter: function(id, is_requested = 0)
        {
            this.editParameterId = -1;
            this.displayParametersForm = id;
            this.newParameterRatingMin = 0;
            this.newParameterRatingMax = 20;
            this.newParameterName = "";
            this.newParameterLabVisible = 0;
            this.newParameterIsRequested = 0;
            if (is_requested == 1) {
                this.newParameterRatingMax = 1;
                this.newParamaterIsRequested = 1;
            }
            $('#newParameterName').focus();
            setTimeout(function() {
                $('#newParameterName').focus();
            }, 200);
        },
        getParamsCount: function (index) {
            try {
                return this.categoryData[index]["parameters"].length;
            } catch (e) {

            }
            return 0;
        },
        addParameterSave: function (id, index) {
            if (this.newParameterName == '') {
                alert('Proszę podać nazwę nowego parametru!');
            } else {
                let tmp = {
                    "name": this.newParameterName,
                    "rating_max": this.newParameterRatingMax,
                    "rating_min": this.newParameterRatingMin,
                    "visible_for_lab": this.newParameterLabVisible,
                    "category_id": id
                };
                try {
                    this.categoryData[index]['parameters'].push(tmp);
                    this.displayParametersForm = -1;
                    this.saveForm();
                } catch (e) {
                    alert ("Wystąpił problem przy dodawaniu parametru, proszę spróbować raz jeszcze");
                }
            }
        },
        editParameterSave: function (id, index) {
            if (this.newParameterName == '') {
                alert('Proszę podać nazwę nowego parametru!');
            } else {
                let tmp = {
                    "name": this.newParameterName,
                    "rating_max": this.newParameterRatingMax,
                    "rating_min": this.newParameterRatingMin,
                    "visible_for_lab": this.newParameterLabVisible,
                    "category_id": id
                };
                try {
                    this.categoryData[index]['parameters'][this.tmpIndex2] = tmp;
                    this.editParameterId = -1;
                    this.saveForm();
                } catch (e) {
                    alert ("Wystąpił problem przy dodawaniu parametru, proszę spróbować raz jeszcze");
                }
            }
        },
        cancelParameterSave: function(id, index) {
            this.displayParametersForm = -1;
        },
        deleteCategory: function(index)
        {
            let conf = confirm("Czy na pewno chcesz usunąć kategorię i parametry?");
            if (conf) {
                try {
                    if (typeof this.categoryData == 'object') {
                        let tmp = [];
                        let max = Object.keys(this.categoryData).length;
                        for (let i =0; i < max; i++) {
                            if (i != index) {
                                tmp.push(this.categoryData[i]);
                            }
                        }
                        this.categoryData = tmp;
                        this.saveForm();
                    }
                } catch (e) {
                    console.log(e);
                }
            }
        },
        deleteParameter: function(index, index2) {
            let conf = confirm("Czy na pewno usunąć dany parametr?");
            if (conf) {
                try {
                    let parameters = this.categoryData[index]['parameters'];
                    let tmp = [];
                    if (parameters.length > 0) {
                        let max = parameters.length;
                        for (let i = 0; i< max; i++) {
                            if (i != index2) {
                                tmp.push(parameters[i]);
                            }
                        }
                        this.categoryData[index]["parameters"] = tmp;
                        this.saveForm();
                    }
                } catch (e) {

                }
            }
        },

        saveForm: function() {
            // console.log(this.saveUrl);
            let formData = this.categoryData;
            console.log(formData);
            toastr.info('Proszę czekać, zapisuję dane');
            axios.post(this.saveUrl, formData).then(function (response) {
                if (response.status == 200) {
                    toastr.success('Dane zostały zapisane poprawnie');
                } else {
                    toastr.error('Wystąpił błąd przy zapisie danych');
                }
            });
        }

    },
    computed: {
        isCategoriesEmpty: function() {
            let self = this;
            console.log(self.categories);
            if (typeof this.categoryData == 'object' && self.categoryData.length == 0) {
                return true;
            }
            if ((Array.isArray(self.categoryData) && self.categoryData.length == 0)) {
                return true;
            }
            return false;
        }
    }
}
</script>
