import {Component, OnInit} from '@angular/core';
import {MailingListService} from '../../../services/mailing-list.service';
import {ActivatedRoute} from '@angular/router';

@Component({
    selector: 'km-mailing-list',
    templateUrl: './mailing-list.component.html',
    styleUrls: ['./mailing-list.component.sass']
})
export class MailingListComponent implements OnInit {

    public mailingList: any = {};
    public proposedKeyOk = true;
    public subscribers: any = [];

    private mailingListId = null;

    constructor(private mailingListService: MailingListService,
                private route: ActivatedRoute) {
    }

    ngOnInit(): void {
        this.route.params.subscribe((params: any) => {
            this.mailingListId = Number(params.id);
            if (this.mailingListId) {
                this.loadMailingList();
            } else {
                this.proposedKeyOk = false;
            }
        });
    }

    public async keyChange(value: string) {
        this.proposedKeyOk = await this.mailingListService.isKeyAvailable(value);
    }

    public async saveMailingList() {
        const mailingListId = await this.mailingListService.saveMailingList(this.mailingList);
        if (!this.mailingListId) {
            location.href = '/mailing-lists/' + mailingListId;
        }
    }

    private async loadMailingList() {
        this.mailingListService.getMailingList(this.mailingListId).then((mailingList: any) => {
            this.mailingList = mailingList;
            this.mailingListService.getSubscribersForMailingList(mailingList.id).then((subscribers: any) => {
                this.subscribers = subscribers;
            });
        }).catch((err: any) => {
            this.mailingList = {};
        });
    }

}
