import {Component, OnInit} from '@angular/core';
import {BehaviorSubject, merge, Subject} from 'rxjs';
import {debounceTime, map, switchMap} from 'rxjs/operators';
import {TemplateService} from '../../services/template.service';
import * as _ from 'lodash';

@Component({
    selector: 'km-templates',
    templateUrl: './templates.component.html',
    styleUrls: ['./templates.component.sass']
})
export class TemplatesComponent implements OnInit {

    public templates: any = [];
    public searchText = new BehaviorSubject('');
    public limit = 1000;
    public offset = 0;
    public page = 1;
    public endOfResults = false;
    public loading = true;

    private reload = new Subject();

    constructor(private templateService: TemplateService) {
    }

    ngOnInit(): void {
        merge(this.searchText, this.reload)
            .pipe(
                debounceTime(300),
                // distinctUntilChanged(),
                switchMap(() =>
                    this.getTemplates()
                )
            ).subscribe((templates: any) => {
            this.endOfResults = templates.length < this.limit;
            this.templates = templates;
            this.templates.map(template => {
                template.sectionSummary = _.map(template.sections, 'title').join(', ');
                template.parameterSummary = _.map(template.parameters, 'title').join(', ');
                return template;
            });
            this.loading = false;
        });

        this.searchText.subscribe(() => {
            this.page = 1;
            this.offset = 0;
        });
    }

    private getTemplates() {
        return this.templateService.filterTemplates(
            this.searchText.getValue() || '',
            this.offset.toString(),
            this.limit.toString()
        ).pipe(map((templates: any) => {
                return templates;
            })
        );
    }

}
