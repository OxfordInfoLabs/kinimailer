import {Component, OnInit} from '@angular/core';
import {ActivatedRoute} from '@angular/router';
import {TemplateService} from '../../../services/template.service';
import {DomSanitizer, SafeHtml} from '@angular/platform-browser';
import * as _ from 'lodash';
import {MatSnackBar} from '@angular/material/snack-bar';

@Component({
    selector: 'km-template',
    templateUrl: './template.component.html',
    styleUrls: ['./template.component.sass']
})
export class TemplateComponent implements OnInit {

    public showIframePane = false;
    public iframeLoading = false;
    public iframeURL: string = null;
    public template: any = {sections: [], parameters: []};
    public templateHTML: SafeHtml = '';
    public options: {
        lineNumbers: true,
        theme: 'ttcn',
        mode: 'htmlmixed'
    };
    public activeTab = 'SECTIONS';
    public selectedSection = null;
    public selectedParameter = null;
    public showUniqueContentSelection = true;

    private templateId;

    constructor(private route: ActivatedRoute,
                private templateService: TemplateService,
                private sanitise: DomSanitizer,
                private snackBar: MatSnackBar) {
    }

    ngOnInit(): void {
        this.route.params.subscribe(params => {
            this.templateId = params.id;
            this.loadTemplate();
        });
    }

    public iframeLoaded(event) {
        setTimeout(() => {
            this.iframeLoading = false;
        }, 500);
    }

    public iframeResizeStart(event) {
        this.showIframePane = true;
    }

    public iframeResizeEnd(event) {
        this.showIframePane = false;
    }

    public async sanitiseHTML(value) {
        const html: string = await this.templateService.evaluateTemplate(this.template);
        // this.iframeLoading = true;
        this.templateHTML = this.sanitise.bypassSecurityTrustHtml(html);
    }

    public copied() {
        this.snackBar.open('Copied to Clipboard', null, {
            duration: 2000,
            verticalPosition: 'bottom'
        });
    }

    public addSection() {
        if (!this.template.sections) {
            this.template.sections = [];
        }
        this.template.sections.unshift({data: {}});
        this.selectedSection = this.template.sections[0];
    }

    public addParameter() {
        if (!this.template.parameters) {
            this.template.parameters = [];
        }
        this.template.parameters.unshift({data: {}});
        this.selectedParameter = this.template.parameters[0];
    }

    public removeSection(index) {
        const message = 'Are you sure you would like to remove this section?';
        if (window.confirm(message)) {
            this.template.sections.splice(index, 1);
            this.selectedSection = null;
        }
    }

    public setSelectedSection(section) {
        if (!section.data || !_.values(section.data).length) {
            section.data = {};
        }
        this.selectedSection = section;
    }

    public setSelectedParameter(parameter) {
        this.selectedParameter = parameter;
    }

    public removeParameter(index) {
        const message = 'Are you sure you would like to remove this parameter?';
        if (window.confirm(message)) {
            this.template.parameters.splice(index, 1);
            this.selectedParameter = null;
        }
    }

    public updateKey(value, section) {
        section.key = _.camelCase(value);
    }

    public uniqueSectionChange(event) {
        this.showUniqueContentSelection = !event.checked;
        if (event.checked) {
            this.template.contentHashSections = [];
        }
    }

    public async save() {
        this.templateId = await this.templateService.saveTemplate(this.template);
        if (!this.template.id) {
            window.location.href  = '/templates/' + this.templateId;
        }
    }

    private async loadTemplate() {
        try {
            this.template = await this.templateService.getTemplate(this.templateId);
        } catch (e) {
            this.template = {sections: [], parameters: []};
        }

        if (this.template.html) {
            this.sanitiseHTML(this.template.html);
        }
    }
}
