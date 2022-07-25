import {Component, OnInit} from '@angular/core';
import {MailingListService} from '../../../../services/mailing-list.service';
import {TemplateService} from '../../../../services/template.service';
import {BehaviorSubject, merge, Subject} from 'rxjs';
import {debounceTime, map, switchMap} from 'rxjs/operators';
import * as _ from 'lodash';

@Component({
    selector: 'km-new-mailing',
    templateUrl: './new-mailing.component.html',
    styleUrls: ['./new-mailing.component.sass']
})
export class NewMailingComponent implements OnInit {

    public mailingLists: any = [];
    public templates: any = [];
    public mailing: any = {mailingListIds: []};
    public mailingListSearchText = new BehaviorSubject('');
    public limit = 10;
    public offset = 0;
    public page = 1;
    public endOfResults = false;
    public loading = true;
    public lodash = _;

    private reload = new Subject();

    constructor(private mailingListService: MailingListService,
                private templateService: TemplateService) {
    }

    ngOnInit(): void {
        merge(this.mailingListSearchText, this.reload)
            .pipe(
                debounceTime(300),
                // distinctUntilChanged(),
                switchMap(() =>
                    this.getMailingLists()
                )
            ).subscribe((mailingLists: any) => {
            this.endOfResults = mailingLists.length < this.limit;
            this.mailingLists = mailingLists;
            this.loading = false;
        });

        this.mailingListSearchText.subscribe(() => {
            this.page = 1;
            this.offset = 0;
        });
    }

    public updateMailingListIds(mailingList) {
        if (!this.mailing.mailingListIds) {
            this.mailing.mailingListIds = [];
        }
        if (!_.find(this.mailing.mailingListIds)) {
            this.mailing.mailingListIds.push(mailingList.id);
        } else {
            _.pull(this.mailing.mailingListIds, mailingList.id);
        }
        console.log(this.mailing.mailingListIds);
    }

    public search(event: any) {
        this.mailingListSearchText.next(event.target.value);
    }

    private getMailingLists() {
        return this.mailingListService.filterMailingList(
            this.mailingListSearchText.getValue() || '',
            this.offset.toString(),
            this.limit.toString()
        ).pipe(map((mailingLists: any) => {
                return mailingLists;
            })
        );
    }

}
