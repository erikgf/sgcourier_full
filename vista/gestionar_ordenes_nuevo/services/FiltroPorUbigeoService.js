const FiltroPorUbigeoService = function(){
    this.filtrarProvincias = ({provinciasTotales, departamento = ""}) => {
        if (departamento === ""){
            return {
                distritos : [],
                provincias: []
            };
        }

        const registrosProvincias = provinciasTotales.filter(prov => {
            return prov.departamento === departamento;
        }).map((prov => {
            return {
                id: prov.provincia,
                descripcion: prov.provincia
            }
        }));

        return {
            distritos: [],
            provincias : registrosProvincias
        };
    };

    this.filtarDistritos = ({distritosTotales, departamento = "", provincia = ""}) => {
        if (departamento === "" || provincia === ""){
            return {
                distritos: []
            };
        }

        const registrosDistritos = distritosTotales.filter(distr => {
                return distr.departamento === departamento && 
                            distr.provincia === provincia;
            }).map((distr => {
                return {
                    id: distr.distrito,
                    descripcion: distr.distrito
                }
            }));

        return {
            distritos: registrosDistritos
        }
    };

    this.filtrarOrdenes = ({registrosTotales, departamento = "", provincia = "", distrito = ""}) => {
        if (departamento === ""){
            return [];
        }

        return registrosTotales.filter(orden => {
            let filtroBooleano = true;
            if (departamento != ""){
                filtroBooleano *= orden.departamento === departamento;
            }

            if (provincia != "" && filtroBooleano){
                filtroBooleano *= orden.provincia === provincia;
            }
            
            if (distrito != "" && filtroBooleano){
                filtroBooleano *= orden.distrito === distrito;
            }

            return filtroBooleano;
        });
    };

    return this;
};