import {Component, OnInit} from '@angular/core';
import {MailingListService} from '../../../services/mailing-list.service';
import {ActivatedRoute} from '@angular/router';
import {MailingService} from '../../../services/mailing.service';

@Component({
    selector: 'km-mailing-list',
    templateUrl: './mailing-list.component.html',
    styleUrls: ['./mailing-list.component.sass']
})
export class MailingListComponent implements OnInit {

    public mailingList: any = {};
    public proposedKeyOk = true;
    public subscribers: any = [];
    public newSubscriber: any = {};
    public showNewSubscriber = true;
    public mailings: any = [];

    private mailingListId = null;

    constructor(private mailingListService: MailingListService,
                private route: ActivatedRoute,
                private mailingService: MailingService) {
    }

    async ngOnInit() {
        this.route.params.subscribe((params: any) => {
            this.mailingListId = Number(params.id);
            if (this.mailingListId) {
                this.loadMailingList();
            } else {
                this.proposedKeyOk = false;
            }
        });

        this.mailings = await this.mailingService.filterMailings('', '0', '1000').toPromise();
    }

    public cancelNewSubscriber() {
        this.newSubscriber = {};
        this.showNewSubscriber = false;
    }

    public async saveNewSubscriber() {
        await this.mailingListService.subscribeToMailingList(this.mailingList.key, this.newSubscriber);
        this.newSubscriber = {};
        this.showNewSubscriber = false;
        this.loadSubscribers();
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
            this.loadSubscribers();
        }).catch((err: any) => {
            this.mailingList = {};
        });
    }

    private async loadSubscribers() {
        this.subscribers = await this.mailingListService.getSubscribersForMailingList(this.mailingList.id);
    }

}
