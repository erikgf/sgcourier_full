{{#each this}}
  <li>
    <a href="#" data-id="{{id_pedido_orden}}" class="item-link item-content">
       <div class="item-inner">
        <div class="item-title-row">
          <div class="item-title"><i>N° Remito: {{codigo_remito}}</i></div>
          <div class="item-after">Paq. <b>{{numero_paquetes}}</b></div>
        </div>
        {{#js_if "this.numero_documento_destinatario  != ''"}}
        <div class="item-subtitle-main">N. Doc.: {{numero_documento_destinatario}}</div>
        {{/js_if}}
        {{#js_if "this.pronabec_sigedo  != ''"}}
        <div class="item-subtitle-main">N. Doc. SIGEDO: {{pronabec_sigedo}}</div>
        {{/js_if}}
        {{#js_if "this.minedu_tipo_documento != ''"}}
        <div class="item-subtitle-main">T.Doc.: {{minedu_tipo_documento}}</div>
        {{/js_if}}
        <div class="item-subtext  text-blue">Fecha: <i>{{fecha_asignado}}</i></div>
        <div class="item-subtitle">{{destinatario}}</div>
        {{#js_if "this.minedu_oficina != ''"}}
        <div class="item-subtitle-main"><b>Oficina:</b> {{minedu_oficina}}</div>
        {{/js_if}}
        {{#js_if "this.pronied_unidad_organica != ''"}}
        <div class="item-subtitle-main"><b>Oficina:</b> {{pronied_unidad_organica}}</div>
        {{/js_if}}
        {{#js_if "this.pronabec_oficina != ''"}}
        <div class="item-subtitle-main"><b>Oficina:</b> {{pronabec_oficina}}</div>
        {{/js_if}}
        <div class="item-subtext"><b>Dir.:</b> {{direccion_uno}}</div>
        {{#js_if "this.direccion_dos  != ''"}}
        <div class="item-subtext"><b>Dir. 2:</b> {{direccion_dos}}</div>
        {{/js_if}}
        {{#js_if "this.referencia  != ''"}}
        <div class="item-subtext"><b>Ref:</b> {{referencia}}</div>
        {{/js_if}}
        {{#js_if "this.tipo_envio  != ''"}}
        <div class="item-subtext"><b>Ref:</b> {{tipo_envio}}</div>
        {{/js_if}}
      </div>
    </a>
  </li>
  {{else}}
  <li class="item-content">
    <i >No hay asignaciones disponibles</i>                         
  </li>
{{/each}}
